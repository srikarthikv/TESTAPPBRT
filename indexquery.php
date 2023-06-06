<!DOCTYPE html>
<html>
<head>
    <title>BRT-GPT</title>
    <style>
        /* CSS styles omitted for brevity */
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            <h1>BRT-GPT</h1>
        </div>
        <div class="chat-body">
            <div class="message bot-message">Welcome! How can I assist you today?</div>
        </div>
        <form action="" method="POST">
            <div class="options-container">
                <label for="compress-option">Compress:</label>
                <select name="compress-option" id="compress-option">
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                </select>
            </div>

            <div class="options-container">
                <label for="vectorstore-option">Select Vectorstore:</label>
                <select name="vectorstore-option" id="vectorstore-option">
                    <option value="vectorstore1">Vectorstore 1</option>
                    <option value="vectorstore2">Vectorstore 2</option>
                    <option value="vectorstore3">Vectorstore 3</option>
                    <!-- Add more options as needed -->
                </select>
            </div>

            <div class="input-container">
                <input type="text" name="question" id="question" required>
                <button type="submit">Send</button>
            </div>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Retrieve the question, compress option, and vectorstore option from the form
            $question = $_POST['question'];
            $compressOption = $_POST['compress-option'];
            $vectorstoreOption = $_POST['vectorstore-option'];

            // Set the request URL
            $url = 'https://10.1.0.4:8000/question';

            // Set the request data
            $data = array(
                'question' => $question,
                'compress' => $compressOption,
                'vectorstore' => $vectorstoreOption,
            );

            // Initialize cURL session
            $ch = curl_init();

            // Set cURL options
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            // Execute the request
            $response = curl_exec($ch);

            // Check for errors
            if (curl_errno($ch)) {
                echo '<div class="message bot-message">Error: ' . curl_error($ch) . '</div>';
            } else {
                // Output the user's message
                echo '<div class="message user-message">' . $question . '</div>';
                // Output the bot's response
                echo '<div class="message bot-message">' . $response . '</div>';
            }

            // Close cURL session
            curl_close($ch);
        }
        ?>
    </div>
</body>
</html>
