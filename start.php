<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panorama Explorer - Welcome</title>
    <link rel="stylesheet" href="assets/css/start.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f0f7ef 0%, #e8f5e6 100%);
            height: 800px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            overflow: hidden;
        }

        .container-start {
            background: white;
            border-radius: 20px;
            padding: 30px 40px;
            max-width: 900px;
            box-shadow: 0 20px 60px rgba(139, 200, 136, 0.15);
            border: 2px solid #c8e6c4;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h1 {
            color: #2d2d2d;
            font-size: 2em;
            margin-bottom: 8px;
            text-align: center;
        }

        .preview-image {
            width: 100%;
            max-width: 300px;
            height: auto;
            border-radius: 12px;
            margin: 20px auto;
            display: block;
            box-shadow: 0 8px 20px rgba(139, 200, 136, 0.25);
            border: 3px solid #b8ddb5;
        }

        .instructions {
            margin-bottom: 20px;
        }

        .instruction-item {
            display: flex;
            align-items: start;
            margin-bottom: 0;
            padding: 15px 20px;
            background: #f5faf4;
            border-radius: 12px;
            border: 2px solid #d4ead1;
            transition: transform 0.2s;
        }

        .instruction-item:hover {
            transform: translateX(5px);
            border-color: #a8d5a3;
            background: #eef7ec;
        }

        .icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #a8d5a3 0%, #8bc888 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            flex-shrink: 0;
            font-size: 20px;
        }

        .instruction-content h3 {
            color: #333;
            margin-bottom: 3px;
            font-size: 1em;
        }

        .instruction-content p {
            color: #666;
            line-height: 1.4;
            font-size: 0.9em;
        }

        .button-container {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 35px;
            font-size: 1em;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, #a8d5a3 0%, #8bc888 100%);
            color: #2d2d2d;
            box-shadow: 0 4px 15px rgba(168, 213, 163, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(168, 213, 163, 0.5);
            background: linear-gradient(135deg, #8bc888 0%, #6fb86b 100%);
        }

        .btn-secondary {
            background: white;
            color: #c74444;
            border: 2px solid #c74444;
        }

        .btn-secondary:hover {
            background: #c74444;
            color: white;
        }

        @media (max-width: 600px) {
            .container-start {
                padding: 30px 20px;
            }

            h1 {
                font-size: 2em;
            }

            .instruction-item {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .icon {
                margin-right: 0;
                margin-bottom: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container-start">
        <h1>Panorama verkenner</h1>


        <img src="assets/img/start.png" alt="Panorama Preview" class="preview-image">

        <div class="instructions">
            <div class="instruction-item">
                <div class="icon">🎯</div>
                <div class="instruction-content">
                    <h3>Hoe te navigeren</h3>
                    <p>Navigeren Scroll naar links en rechts om het panorama te verkennen. Klik op hotspots voor informatie. Gebruik de minikaart onderaan om snel naar verschillende secties te springen.</p>
                </div>
            </div>
        </div>

        <div class="button-container">
            <a href="panorama.php" class="btn btn-primary">Panorama</a>
        </div>
    </div>

    <script>
        // No scripts needed for simple link navigation
    </script>
</body>
</html>