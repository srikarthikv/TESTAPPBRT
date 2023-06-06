<!DOCTYPE html>
<html>

<head>
    <title>File Upload</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #2196f3;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            color: #fff;
            font-weight: bold;
            padding: 20px;
            margin: 0;
        }

        form {
            max-width: 400px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="file"] {
            margin-bottom: 10px;
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .success-message {
            text-align: center;
            color: #4caf50;
            margin-top: 10px;
        }

        .error-message {
            text-align: center;
            color: #3f51b5;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <h1>BRTGPT</h1>

    <form action="" method="POST" enctype="multipart/form-data">
        <input type="file" name="uploadedFile" />
        <input type="submit" value="Upload" />
    </form>

    <?php
    // Enable error reporting
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Required Azure Blob Storage libraries
    require_once 'vendor/autoload.php';

    use MicrosoftAzure\Storage\Blob\BlobRestProxy;
    use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
    use MicrosoftAzure\Storage\Blob\Models\Blob;
    use MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions;

    // Azure Blob Storage connection string
    $connectionString = 'DefaultEndpointsProtocol=https;AccountName=gptdemo7020140432;AccountKey=k3z0/JCQH3yV/9iSceGe+s1dtdIUbp8anSUQ/a0sDsrw34tuFHfd7usPn42bCvjaUdzlfpNvA09O+AStCRDO3w==;EndpointSuffix=core.windows.net';
    $directoryName = 'documents/';

    // Create a BlobRestProxy instance
    $blobClient = BlobRestProxy::createBlobService($connectionString);

    // Check if a file is uploaded
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['uploadedFile'])) {
        $file = $_FILES['uploadedFile'];

        // Generate a unique name for the file
        $fileName = $directoryName . $file['name'];

        // Set the container name where the file will be stored
        $containerName = 'azureml-blobstore-fa29c537-9f94-4f15-8679-5f1e2fd597e4';

        try {
            // Upload the file to Azure Blob Storage
            $blobClient->createBlockBlob($containerName, $fileName, fopen($file['tmp_name'], 'r'));

            echo '<p class="success-message">File uploaded successfully!</p>';

            // Send a POST request to the Flask server with the file path
            $flaskServerUrl = 'https://10.1.0.4:8000/embed';
            $postData = array('file_path' => $fileName);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $flaskServerUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_exec($ch);
            curl_close($ch);
        } catch (ServiceException $e) {
            $code = $e->getCode();
            $error_message = $e->getMessage();
            echo '<p class="error-message">Failed to upload the file. Error message: ' . $error_message . '</p>';
        }
    }

    ?>

</body>

</html>

