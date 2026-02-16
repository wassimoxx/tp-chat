<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Group Chat - Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        h1 {
            color: #fff;
            text-align: center;
            margin-bottom: 10px;
            font-weight: 600;
            font-size: 28px;
        }

        .subtitle {
            color: rgba(255, 255, 255, 0.6);
            text-align: center;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .error-message {
            background: rgba(231, 76, 60, 0.2);
            border: 1px solid rgba(231, 76, 60, 0.5);
            color: #e74c3c;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 500;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.05);
            color: #fff;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }

        input::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(52, 152, 219, 0.3);
        }

        button:active {
            transform: translateY(0);
        }

        .info {
            margin-top: 20px;
            padding: 15px;
            background: rgba(52, 152, 219, 0.1);
            border-radius: 10px;
            border: 1px solid rgba(52, 152, 219, 0.2);
        }

        .info p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 13px;
            line-height: 1.5;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h1>💬 Group Chat</h1>
        <p class="subtitle">Enter your credentials to join the conversation</p>

        <?php if (!empty($error)): ?>
            <div class="error-message">
                <?= \Core\View::escape($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/login">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Choose a username" required
                    autocomplete="username" maxlength="50">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Your password" required
                    autocomplete="current-password">
            </div>

            <button type="submit">Join Chat</button>
        </form>

        <div class="info">
            <p>New user? Just enter your desired username and password to create an account and join. Existing users
                must use their registered password.</p>
        </div>
    </div>
</body>

</html>