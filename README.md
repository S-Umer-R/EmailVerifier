# Email Verifier

Email Verifier is a web application that allows you to verify the validity of email addresses stored in a CSV file. It utilizes client-side JavaScript and server-side PHP to process and verify the emails.

## Features

- Upload a CSV file containing a list of email addresses for verification.
- Display the verification result for each email address in a table.
- Provide options to download the verification result, valid emails only, and invalid emails only as CSV files.
- Show a loading spinner during the verification process.

## Installation

To use the Email Verifier application, follow these steps:

1. Clone the repository to your local machine or download the source code.
2. Ensure you have a web server installed (such as Apache or Nginx) and PHP support enabled.
3. Copy the repository files to your web server's document root directory.
4. Ensure that the `upload_max_filesize` and `post_max_size` directives in your PHP configuration allow file uploads of the desired size.
5. Open the `index.html` file in a web browser.

## Usage

1. Launch the Email Verifier application by opening the `index.html` file in a web browser.
2. Click on the "Choose File" button to select a CSV file containing the email addresses to verify. The file must be in CSV format, with each email address in a separate row.
3. Once the file is selected, the name of the file will be displayed next to the "Choose File" button.
4. Click the "Verify" button to start the verification process. The application will upload the file to the server and initiate the verification.
5. During the verification process, a loading spinner will be displayed to indicate that the application is working.
6. Once the verification is complete, the verification result for each email address will be displayed in a table.
7. You can download the verification result as a CSV file by clicking the "Download Result" link.
8. Additionally, you can download a CSV file containing only the valid email addresses by clicking the "Download Valid Only" link, or a file with only the invalid email addresses by clicking the "Download Invalid Only" link.

## How It Works

The Email Verifier application uses JavaScript and PHP to process and verify the email addresses.

- The `index.html` file contains the HTML structure of the application, including the file input, verify button, loading spinner, and result table.
- The `style.css` file defines the visual appearance of the application.
- The `script.js` file handles the client-side logic, including file selection, verification initiation, and result display.
- The `verify.php` file is responsible for server-side processing. It verifies the email addresses in the uploaded CSV file using the `verifyEmail` function and returns the verification result as a CSV-formatted response.

The verification process works as follows:

1. The user selects a CSV file containing the email addresses to verify.
2. When the user clicks the "Verify" button, the JavaScript code reads the selected file and sends it to the server using AJAX.
3. On the server side, the `verify.php` script receives the uploaded file and processes it using the `verifyEmailsInFile` function.
4. The `verifyEmailsInFile` function reads the CSV file line by line and initiates asynchronous verification for each email address using cURL.
5. The server sends requests to the mail server of each email address and checks the response to determine the validity of the email.
6. Once all email addresses are verified, the server generates a CSV response with the verification result for each email.
7. The response is sent back to the client, and the JavaScript code updates the

## Limitations:

Here are some limitations to consider for the Email Verifier application:

1. Email Server Limitations: The effectiveness of email verification relies on the responses received from the email servers. Some email servers may have limitations or rate limits on the number of requests they can handle. If the application sends a high volume of requests to a specific email server, it could lead to temporary blocks or degraded performance.

2. Accuracy: Email verification is not always 100% accurate. The verification process relies on checking the response from the email server, but there can be cases where the server response is inconclusive or misleading. Additionally, temporary server issues or misconfigurations can result in false positives or false negatives. It's important to understand that email verification provides a likelihood of validity rather than a definitive answer.

3. Compatibility: The Email Verifier application may have browser compatibility limitations. Different browsers may interpret JavaScript and HTML code differently, which could impact the functionality or appearance of the application. Test the application across various browsers to ensure compatibility and provide fallback options if necessary.

