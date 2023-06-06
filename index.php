<!DOCTYPE html>
<html>

<head>
    <title>File Upload</title>
</head>

<body>
    <h1>Upload a File</h1>

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

            echo 'File uploaded successfully!';

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
            echo "Failed to upload the file. Error message: $error_message";
        }
    }

    ?>

</body>

</html>
