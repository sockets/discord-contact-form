<?php

    //  Built by Joseph
    //  Github @sockets
    //  LinkedIn @sockets



    // Config Settings (All are required)
    $HCAPTCHA_SITE_KEY = "";
    $HCAPTCHA_SECRET_KEY = "0x...";
    $WEBHOOK_URL = "";
    
    // Check if the form was submitted
    if(isset($_POST['submit'])){
        // Check if HCaptcha data was sent
        if(isset($_POST['h-captcha-response']) && !empty($_POST['h-captcha-response'])){
            // Check for HTML Form Data
            if(
                isset($_POST['name']) && !empty($_POST['name'])
                && isset($_POST['email']) && !empty($_POST['email'])
                && isset($_POST['message']) && !empty($_POST['message'])
            ){
                // Seralize Data
                $responseToken=htmlspecialchars($_POST['h-captcha-response']);
                $name = htmlspecialchars($_POST['name']);
                $email = htmlspecialchars($_POST['email']);
                $message = htmlspecialchars($_POST['message']);


                // HCaptcha Verification
                $data = array(
                    'secret' => $HCAPTCHA_SECRET_KEY,
                    'response' => $responseToken
                );
                $verify = curl_init();
                curl_setopt($verify, CURLOPT_URL,   "https://hcaptcha.com/siteverify");
                curl_setopt($verify, CURLOPT_POST, true);
                curl_setopt($verify, CURLOPT_POSTFIELDS, http_build_query($data));
                curl_setopt($verify, CURLOPT_RETURNTRANSFER, true);
                $verifyResponse = curl_exec($verify);        
                $responseData = json_decode($verifyResponse);

                // If HCaptcha has a successful verification
                if($responseData->success){
                    
                    // Define curl options (You can edit the embed, just be careful)
                    $opts = [
                        "http" => [
                            "method" => "POST",
                            "header" => "Content-type: application/json\r\n",
                            "content" => "{
                                \"content\": null,
                                \"embeds\": [
                                    {
                                    \"title\":  \"New Contact\",
                                    \"description\":  \"```".$message."```\",
                                    \"color\": 10824650,
                                    \"fields\": [
                                        {
                                        \"name\": \"Name:\",
                                        \"value\": \"`".$name."`\",
                                        \"inline\": true
                                        },
                                        {
                                        \"name\": \"Email:\",
                                        \"value\": \"`".$email."`\",
                                        \"inline\": true
                                        },
                                        {
                                        \"name\": \"Timestamp:\",
                                        \"value\": \"`".date('m/d/Y h:i:sA')."`\",
                                        \"inline\": true
                                        }
                                    ]
                                    }
                                ],
                                \"username\": \"Website Logger\"
                            }"
                        ]
                    ];
                    
                    $context = stream_context_create($opts);
                    $users = file_get_contents($WEBHOOK_URL, false, $context);
                    $usersJson = json_decode($users, true);

                    // Set Success Message
                    $successMessage = "Your message was successfuly sent!";
                    // Reset Forms
                    $name = '';
                    $email = '';
                    $message = '';

                } else {
                    // Set Error Message
                    $errorMessage = 'hCaptcha verification failed. Please try again.';
                }
            } else {
                // Set Error Message
                $errorMessage = 'Please enter all fields.';
            }
        } else {
            // Set Error Message
            $errorMessage = 'Please click on the hCaptcha button.';
        }
    } else {
        // Reset Forms
        $errorMessage = '';
        $successMessage = '';
        $name = '';
        $email = '';
        $message = '';
    }
?>
<html>
    <head>
        <title>Contact Form</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://www.hCaptcha.com/1/api.js" async defer></script>
    </head>
    <body>
        <div class="relative flex min-h-screen flex-col justify-center overflow-hidden bg-gray-50 py-6 sm:py-12">
            <div class="relative bg-white px-6 pt-10 pb-8 shadow-xl ring-1 ring-gray-900/5 sm:mx-auto sm:max-w-xl sm:rounded-lg sm:px-10">
                <div class="mx-auto max-w-lg">
                <div class="divide-y divide-gray-300/50">
                    <?php if(!empty($errorMessage)){ ?><div class="text-red-500"><?php echo $errorMessage; ?></div><?php } ?>
                    <?php if(!empty($successMessage)){ ?><div class="text-green-500"><?php echo $successMessage; ?></div><?php } ?>
                    <div class="space-y-6 py-2 text-base leading-7 text-gray-600">
                        <form action="" method="POST">
                            <div class="grid grid-cols-2 gap-x-3">
                                <label for="name" class="w-full text-left text-sm font-semibold tracking-wide text-gray-400">
                                    Name
                                    <span class="cursor-help text-red-500" title="Required"> * </span>
                                    <input name="name" id="name" value="<?php echo !empty($name)?$name:''; ?>" class="my-2 w-full rounded-lg border-[1px] border-gray-200 bg-transparent p-2 text-gray-400 outline-none duration-200 focus:border-blue-500" type="text" placeholder="John Doe" required />
                                </label>
                                <label for="email" class="w-full text-left text-sm font-semibold tracking-wide text-gray-400">
                                    Email
                                    <span class="cursor-help text-red-500" title="Required"> * </span>
                                    <input name="email" id="email" value="<?php echo !empty($email)?$email:''; ?>" class="my-2 w-full rounded-lg border-[1px] border-gray-200 bg-transparent p-2 text-gray-400 outline-none duration-200 focus:border-blue-500" type="email" placeholder="johndoe@josephclardy.com" required />
                                </label>
                            </div>
                            <label for="message" class="w-full text-left text-sm font-semibold tracking-wide text-gray-400">
                                Message
                                <span class="cursor-help text-red-500" title="Required"> * </span>
                                <textarea name="message" id="message" class="my-2 max-h-40 min-h-[80px] w-full rounded-lg border-[1px] border-gray-200 bg-transparent p-2 text-gray-400 outline-none duration-200 focus:border-blue-500" type="text" placeholder="Write me a message!" required><?php echo !empty($message)?$message:''; ?></textarea>
                            </label>
                            <!-- HCaptcha Field -->
                            <div class="h-captcha" data-sitekey="<?php echo $HCAPTCHA_SITE_KEY ?>"></div>
                            <button name="submit" type="submit" class="font-poppins group ml-auto flex rounded-md border border-transparent bg-blue-500 px-8 py-2 text-sm font-medium text-white duration-200 hover:bg-blue-400 motion-reduce:transition-none">Send Message</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- 
            Make sure to give credits!
        -->
        <p class="mt-8 text-center text-gray-500">Built by <a class="text-blue-400" href="https://josephclardy.com/">Joseph Clardy</a></p>

    </body>
</html>