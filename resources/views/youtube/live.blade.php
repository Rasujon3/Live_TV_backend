<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Live TV</title>
    <style>
        .video-container {
            position: relative;
            width: 100%;
            height: 100%;
        }

        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        @media (min-width: 768px) {
            .video-wrapper {
                width: 50%;
                height: 50vh;
            }
        }

        @media (max-width: 767px) {
            .video-wrapper {
                width: 100%;
                height: 100vh;
            }
        }
    </style>
</head>
<body>
<div class="video-wrapper">
    <div class="video-container">
        <iframe
            src="https://www.youtube.com/embed/{{ $videoId }}"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen>
        </iframe>
    </div>
</div>
</body>
</html>
