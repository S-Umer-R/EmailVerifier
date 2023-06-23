document.getElementById('fileInput').addEventListener('change', function() {
  var fileName = document.getElementById('fileInput').value.split('\\').pop();
  document.getElementById('fileName').textContent = fileName;
});

document.getElementById('verifyBtn').addEventListener('click', function() {
  var verifyBtn = document.getElementById('verifyBtn');
  var fileInput = document.getElementById('fileInput');
  var file = fileInput.files[0];

  if (file) {
    verifyBtn.disabled = true;
    fileInput.disabled = true;

    var formData = new FormData();
    formData.append('file', file);

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'verify.php', true);

    xhr.upload.addEventListener('progress', function(event) {
      if (event.lengthComputable) {
        var percent = (event.loaded / event.total) * 100;
        document.getElementById('loader').style.display = 'block';
        console.log('Upload progress: ' + percent + '%');
      }
    });

    xhr.onreadystatechange = function() {
      if (xhr.readyState === XMLHttpRequest.DONE) {
        if (xhr.status === 200) {
          document.getElementById('loader').style.display = 'none';
          document.getElementById('resultContainer').style.display = 'block';
          var response = xhr.responseText;
          displayVerificationResult(response);
        } else {
          console.log('Error occurred. Server returned status code: ' + xhr.status);
        }

        verifyBtn.disabled = false;
        fileInput.disabled = false;
      }
    };

    xhr.send(formData);
  }
});

function displayVerificationResult(response) {
  var resultTableBody = document.getElementById('resultTable').getElementsByTagName('tbody')[0];
  resultTableBody.innerHTML = '';

  var rows = response.split('\n');
  var validEmails = [];
  var invalidEmails = [];

  for (var i = 0; i < rows.length; i++) {
    var columns = rows[i].split(',');
    if (columns.length === 2) {
      var email = columns[0];
      var verification = columns[1];

      var row = document.createElement('tr');
      var emailCell = document.createElement('td');
      var verificationCell = document.createElement('td');

      emailCell.textContent = email;
      verificationCell.textContent = verification;

      row.appendChild(emailCell);
      row.appendChild(verificationCell);

      resultTableBody.appendChild(row);

      if (verification === 'Valid') {
        validEmails.push(email);
      } else if (verification === 'Invalid') {
        invalidEmails.push(email);
      }
    }
  }

  var originalFileName = document.getElementById('fileName').textContent.replace('.csv', '');
  var encodedContent = encodeURIComponent(response);
  var dataUri = 'data:text/csv;charset=utf-8,' + encodedContent;

  var downloadBtn = document.getElementById('downloadBtn');
  downloadBtn.href = dataUri;
  downloadBtn.download = originalFileName + '_result.csv';

  var downloadValidBtn = document.getElementById('downloadValidBtn');
  var validEmailsContent = validEmails.join('\n');
  var validEmailsUri = 'data:text/csv;charset=utf-8,' + encodeURIComponent(validEmailsContent);
  downloadValidBtn.href = validEmailsUri;
  downloadValidBtn.download = originalFileName + '_valid.csv';

  var downloadInvalidBtn = document.getElementById('downloadInvalidBtn');
  var invalidEmailsContent = invalidEmails.join('\n');
  var invalidEmailsUri = 'data:text/csv;charset=utf-8,' + encodeURIComponent(invalidEmailsContent);
  downloadInvalidBtn.href = invalidEmailsUri;
  downloadInvalidBtn.download = originalFileName + '_invalid.csv';
}
