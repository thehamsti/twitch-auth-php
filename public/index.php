<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
	<link rel="stylesheet" type="text/css" media="screen" href="css/main.css" />
	<title>Authenticate here</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>
	<script src="js/main.js" async defer></script>
</head>

<body>
	<div class="login_wrapper">
		<div class="login_inner">
			<div class="login">
				<div class="logo"></div>
				<div class="login_header">Authenticate with Twitch <br />Please authenticate below!</div>
				<div class="login_button button">
					<?php
					// In order to setup the file 1. Setup Database, 2. Fill in client_id, client_secret, and redirect url
					// 3. Setup scopes depending on your project needs 
					// 1. Set database information
					$pdo = new PDO("mysql:host=localhost;dbname=databasenamehere", 'dbusername', 'Password');
					$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
					// 2. Set client_id and client_secret as well as redirect url
					$client_id = 'oa7rqegckedndfdiptxd3byc231r2z3';
					$client_secret = 'c8gvk8gl12cy0dvdyjbd23ddfwn2e';
					$redirect_url = 'url to this page';

					if ($_GET['code']) {
						$token_url = 'https://id.twitch.tv/oauth2/token';
						$data = array(
							'client_id' => $client_id,
							'client_secret' => $client_secret,
							'grant_type' => 'authorization_code',
							'redirect_uri' => $redirect_url,
							'code' => $_GET['code']
						);
						$curl = curl_init($token_url);
						curl_setopt($curl, CURLOPT_POST, true);
						curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
						curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
						$result = curl_exec($curl);
						$result = json_decode($result, true);

						$i = curl_getinfo($curl);
						curl_close($curl);
						if ($i['http_code'] == 200) {
							$curl = curl_init('https://api.twitch.tv/kraken/user');
							curl_setopt($curl, CURLOPT_HTTPHEADER, array(
								'Accept: application/vnd.twitchtv.v5+json',
								'Client-ID: ' . $client_id,
								'Authorization: OAuth ' . $result['access_token']
							));
							curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
							$user = curl_exec($curl);

							$i = curl_getinfo($curl);
							curl_close($curl);
							if ($i['http_code'] == 200) {
								$user = json_decode($user, true);
								if ($user['partnered']) {
									$partnered = 1;
								} else {
									$partnered = 0;
								}
								// Insert into a database for use in your app
								$sql = "INSERT INTO users (tw_id, tw_token, tw_refresh, tw_name, partnered, tw_logo) VALUES ('" . $user['_id'] . "', '" . $result['access_token'] . "', '" . $result['refresh_token'] . "', '" . $user['display_name'] . "', '" . $partnered . "', '" . $user['logo'] . "') ON DUPLICATE KEY UPDATE tw_token = '" . $result['access_token'] . "', tw_refresh ='" . $result['refresh_token'] . "';";
								$res = $pdo->query($sql);
								// Redirect to wherever you want to redirect with header
								header('Location: /dashboard');
							} else {
								echo '<p>An error occured, please <a href="/">click here and try again</a></p>';
							}
						} else {
							echo '<p>An error occured, please <a href="/">click here and try again</a></p>';
						}
					} else {
						// 3. These are the scopes you get permission when getting the token
						// Find more about scopes here - https://dev.twitch.tv/docs/authentication#scopes
						$scopes = array(
							'channel_subscriptions' => 1,
							'channel_check_subscription' => 1,
							'channel_editor' => 1,
							'user_read' => 1,
							'bits:read' => 1,
							'channel:moderate' => 1,
							'channel:read:redemptions' => 1,
						);
						$req_scope = '';
						foreach ($scopes as $scope => $allow) {
							if ($allow) {
								$req_scope .= $scope . '+';
							}
						}

						$req_scope = substr($req_scope, 0, -1);
						$auth_url = 'https://id.twitch.tv/oauth2/authorize?response_type=code';
						$auth_url .= '&client_id=' . $client_id;
						$auth_url .= '&redirect_uri=' . $redirect_url;
						$auth_url .= '&scope=' . $req_scope;

						echo '<a href="' . $auth_url . '">';
					}

					?>Log in</a>
				</div>
			</div>
		</div>
	</div>

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
</body>

</html>