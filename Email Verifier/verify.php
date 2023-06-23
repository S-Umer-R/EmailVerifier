<?php

// Increase maximum execution time
ini_set('max_execution_time', 300); // Set maximum execution time to 5 minutes (300 seconds)

// Increase memory limit
ini_set('memory_limit', '256M'); // Set memory limit to 256 megabytes

function verifyEmail($email)
{
  if (!strpos($email, '@')) {
    return "Invalid"; // Email address is missing the "@" symbol
  }

  list($user, $domain) = explode('@', $email);

  try {
    $records = dns_get_record($domain, DNS_MX);
  } catch (Exception $e) {
    return "DNS Query Failed"; // Handle the DNS query failure exception
  }

  if (!empty($records)) {
    $mx = $records[0]['target'];
    $timeout = 5; // Timeout value in seconds

    $socket = @fsockopen($mx, 25, $errno, $errstr, $timeout);

    if ($socket) {
      stream_set_timeout($socket, $timeout);

      // Get the server's initial response
      $response = fgets($socket);
      if (substr($response, 0, 3) !== '220') {
        fclose($socket);
        return "Invalid"; // Failed to establish connection or invalid response from the server
      }

      // Send the HELO command
      fwrite($socket, "HELO " . $_SERVER['HTTP_HOST'] . "\r\n");
      $response = fgets($socket);
      if (substr($response, 0, 3) !== '250') {
        fclose($socket);
        return "Invalid"; // Invalid response from the server
      }

      // Send the MAIL FROM command
      fwrite($socket, "MAIL FROM: <verify@example.com>\r\n");
      $response = fgets($socket);
      if (substr($response, 0, 3) !== '250') {
        fclose($socket);
        return "Invalid"; // Invalid response from the server
      }

      // Send the RCPT TO command
      fwrite($socket, "RCPT TO: <" . $email . ">\r\n");
      $response = fgets($socket);
      fclose($socket);

      if (substr($response, 0, 3) === '250' || substr($response, 0, 3) === '451') {
        return "Valid"; // Email address is valid and deliverable
      } else {
        return "Invalid"; // Email address is undeliverable
      }
    } else {
      return "Invalid"; // Failed to establish connection with the mail server
    }
  } else {
    return "Invalid"; // No MX records found for the email domain
  }
}

function verifyEmailsInFile($file)
{
  $batchSize = 50; // Number of emails to process in each batch
  $result = array();

  if (($handle = fopen($file, "r")) !== false) {
    $batch = array();
    while (($data = fgetcsv($handle, 1000, ",")) !== false) {
      $email = trim($data[0]);
      $batch[] = $email;

      // Process the batch when it reaches the desired size
      if (count($batch) >= $batchSize) {
        processBatchAsync($batch, $result);
        $batch = array(); // Clear the batch for the next iteration
      }
    }

    // Process any remaining emails in the last batch
    if (!empty($batch)) {
      processBatchAsync($batch, $result);
    }

    fclose($handle);
  }

  header('Content-Type: text/plain');

  $output = '';
  foreach ($result as $row) {
    $output .= implode(',', $row) . "\n";
  }

  echo $output;
}

function processBatchAsync($emails, &$result)
{
  $curlMultiHandler = curl_multi_init();
  $curlHandlers = [];

  foreach ($emails as $email) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $_SERVER['HTTP_HOST']);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_multi_add_handle($curlMultiHandler, $ch);
    $curlHandlers[$email] = $ch;
  }

  do {
    curl_multi_exec($curlMultiHandler, $running);
  } while ($running > 0);

  foreach ($emails as $email) {
    $ch = $curlHandlers[$email];
    $verification = verifyEmail($email);
    $result[] = array($email, $verification);
    curl_multi_remove_handle($curlMultiHandler, $ch);
    curl_close($ch);
  }

  curl_multi_close($curlMultiHandler);
}

if ($_FILES["file"]["error"] === UPLOAD_ERR_OK) {
  $file = $_FILES["file"]["tmp_name"];
  verifyEmailsInFile($file);
}
?>
