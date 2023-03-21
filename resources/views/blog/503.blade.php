@vite(['resources/css/app.scss'])

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>
</head>

<style>
    @keyframes slide-out {
        from {
            transform: translateX(-100%);
        }
        to {
            transform: translateX(0);
        }
    }

    #maxwell_image {
        transform: translateX(-100%);
        animation: slide-out 5s linear 2s forwards;
    }
</style>

<div class="my-2 p-4 font-serif">
    <p class="text-4xl font-bold">Service Unavailable</p>
    <hr class="my-5 border-t-1 border-black">
    <p class="">HTTP Error 503. The service is unavailable</p>

    <div class="mt-10">
        <a href="/">
            <img class="opacity-1" src="/images/maxwell.gif" id="maxwell_image" alt="Maxwell the cat" title="Maxwell the cat">
        </a>
        <audio id="maxwell_audio" autoplay hidden loop>
            <source src="/sounds/maxwell.mp3" type="audio/mpeg">
        </audio>
    </div>
</div>


<script>
    const audio = document.getElementById('maxwell_audio');
    const image = document.getElementById('maxwell_image');
    audio.volume = 0; // Set initial volume to 0
    audio.pause();

    const maxVolume = 1; // Maximum volume (100%)
    let intervalId;

    setTimeout(() => {
        audio.play();
        // Increase volume gradually over 5 seconds
        intervalId = setInterval(() => {
            var incremented_val = Number((audio.volume + 0.01).toFixed(2));
            audio.volume = incremented_val;
            if (audio.volume >= maxVolume) {
                clearInterval(intervalId);
            }
        }, 50); // Change volume every 50 milliseconds
    }, 2000);
</script>
