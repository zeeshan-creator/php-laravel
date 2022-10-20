<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios@1.1.2/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
</head>


<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // All the post variable
    $model_name = $_POST['model_name'];
    $estimated_delivery = $_POST['estimated_delivery'];
    $square = $_POST['square'];
    $door_colors = $_POST['door_colors'];
    $accessories = $_POST['accessories'];
    $remote_controller = $_POST['remote_controller'];
    $excluded_vat = $_POST['excluded_vat'];
    $total_price = $_POST['total_price'];
    $username = $_POST['username'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];

    // initialize a var to store the accessories in the string format from array;
    $accessories_String = "";

    // run loop to convert array to string
    for ($i = 0; $i < count($accessories); $i++) {
        $accessories_String .= $accessories[$i] . ",";
    };

    // make a JSON object for the properties
    $obj = (object) [
        'model_name' => $model_name,
        'estimated_delivery' => $estimated_delivery,
        'square' => $square,
        'door_colors' => $door_colors,
        'accessories' => $accessories_String,
        'remote_controller' => $remote_controller,
        'excluded_vat' => $excluded_vat,
        'total_price' => $total_price,
        'username' => $username,
        'phone' => $phone,
        'email' => $email
    ];
    $post_json = json_encode($obj);

    // Endpoint
    $url = "https://api.hubspot.com/crm/v3/objects/p23171692_garage_Order";

    // Run createOrder function it will return the created object; 
    echo createOrder($url, $post_json);
}

function createOrder($endpoint, $post_json, $token = "pat-na1-95062fb0-766d-4e2a-ba3d-9b9b43816a7b")
{
    $url = $endpoint;

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $headers = array(
        "Authorization: Bearer $token",
        "Content-Type: application/json",
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $data = <<<DATA
    {
        "properties": $post_json
    }
    DATA;

    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

    //for debug only!
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $resp = curl_exec($curl);
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    if ($httpcode == 201) {
        return "Create successfully";
    } else {
        return $resp;
    }
}

?>



<body>
    <div class="container mt-4">

        <form method="post">
            <div class="row">

                <div class="form-group">
                    <label for="model_name">model_name :</label>
                    <input type="text" class="form-control" placeholder="Enter model_name" name="model_name">
                </div>

                <div class="form-group">
                    <label for="estimated_delivery">estimated_delivery :</label>
                    <input type="text" class="form-control" placeholder="Enter estimated_delivery" name="estimated_delivery">
                </div>

                <div class="form-group">
                    <label for="square">square :</label>
                    <input type="text" class="form-control" placeholder="Enter square" name="square">
                </div>

                <div class="form-group">
                    <label for="door_colors">door_colors :</label>
                    <input type="text" class="form-control" placeholder="Enter door_colors" name="door_colors">
                </div>

                <div class="form-group">
                    <input class="form-check-input" type="checkbox" name="accessories[]" id="accessories_1" value="accessories_1">
                    <label class="form-check-label" for="accessories_1">
                        accessories 1
                    </label>

                    <input class="form-check-input" type="checkbox" name="accessories[]" id="accessories_2" value="accessories_2">
                    <label class="form-check-label" for="accessories_2">
                        accessories 2
                    </label>
                </div>

                <div class="form-group">
                    <label for="remote_controller">remote_controller :</label>
                    <input type="text" class="form-control" placeholder="Enter remote_controller" name="remote_controller">
                </div>

                <div class="form-group">
                    <label for="excluded_vat">excluded_vat :</label>
                    <input type="text" class="form-control" placeholder="Enter excluded_vat" name="excluded_vat">
                </div>

                <div class="form-group">
                    <label for="total_price">total_price :</label>
                    <input type="text" class="form-control" placeholder="Enter total_price" name="total_price">
                </div>

                <div class="form-group">
                    <label for="username">username :</label>
                    <input type="text" class="form-control" placeholder="Enter username" name="username">
                </div>


                <div class="form-group">
                    <label for="email">Email address:</label>
                    <input type="email" class="form-control" placeholder="Enter email" name="email">
                </div>

                <div class="form-group">
                    <label for="phone">phone:</label>
                    <input type="number" class="form-control" placeholder="Enter phone" name="phone">
                </div>


            </div>
            <button type="submit" class="btn btn-primary " name="btn-submit">Submit</button>

        </form>

    </div>

</body>




</html>
