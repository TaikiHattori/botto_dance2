<div>
    <!-- It is not the man who has too little, but the man who craves more, that is poor. - Seneca -->
</div>

<x-app-layout>
  {{-- <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      {{ __('再生') }}
    </h2>
  </x-slot> --}}

  <style>
    html, body {
      color: #ffffff; /* テキスト色を白に */
      margin: 0;
      padding: 0;
      height: 100%;
    }

    .text-gray-800, .text-gray-900, .dark\:text-gray-100 {
      color: #000000;
    }
    .container {
      min-height: 100vh; /* コンテナの高さを画面全体に */
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }

    /* 光るエフェクトの追加 */
    .background-circle {
      stroke: #fff;
      stroke-width: 2;
      fill: none;
      opacity: 0.7;
      filter: blur(2px) drop-shadow(0 0 10px rgba(255, 255, 255, 0.5));
    }

    .progress-bar {
      stroke: #D3F3F9;
      stroke-width: 6;
      fill: none;
      stroke-dasharray: 283;
      stroke-dashoffset: 283;
      filter: drop-shadow(0 0 6px #D3F3F9) drop-shadow(0 0 12px #D3F3F9);
    }


    .neon-icon {
    stroke: white;
    filter: drop-shadow(0 0 0px #D3F3F9)
            drop-shadow(0 0 1px #D3F3F9)
            drop-shadow(0 0 3px #D3F3F9)
            drop-shadow(0 0 6px #D3F3F9)
            drop-shadow(0 0 8px #D3F3F9);
    }
  </style>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
          @if ($extractions->isEmpty())
            <p>S3に曲が存在しません。</p>
          @else
            <audio id="audioPlayer"></audio>

            <div class="relative flex justify-center items-center flex-col">
              <!-- 再生バーを円形に変更 -->
              <div class="w-96 h-96" style="transform: rotate(-90deg);">
                <svg class="absolute top-0 left-0 w-full h-full" viewBox="-20 -20 140 140">
                  {{-- <circle cx="50" cy="50" r="45" class="background-circle" /> --}}
                  <circle id="progressCircle" cx="50" cy="50" r="45" class="progress-bar" stroke-linecap="round"/>
                  <image href="{{ asset('storage/images/tsuki.png') }}" x="6" y="6" style="width: 5.5rem;height: 5.5rem;" />
                </svg>
              </div>

              <!-- 再生ボタン -->
              <button id="playButton" class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 hover:opacity-80 text-white font-bold py-2 px-4 rounded-full flex items-center justify-center">
                {{-- <svg class="w-16 h-16 fill-current text-white" viewBox="0 0 24 24">
                  <polygon points="5,3 19,12 5,21" />
                </svg> --}}
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="0.4" stroke-linecap="round" stroke-linejoin="round" class="w-72 h-72 neon-icon"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8"/></svg>
              </button>
            </div>


            <script>
              document.addEventListener('DOMContentLoaded', function() {
                const extractions = @json($extractions);
                const audioPlayer = document.getElementById('audioPlayer');
                const playButton = document.getElementById('playButton');
                const progressCircle = document.getElementById('progressCircle');
                const fadeDuration = 5; // フェードイン・フェードアウトの時間（秒）

                console.log('Extractions:', extractions);

                function shuffle(array) {
                  for (let i = array.length - 1; i > 0; i--) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [array[i], array[j]] = [array[j], array[i]];
                  }
                  return array;
                }

                
                function timeToSeconds(time) {
                  const parts = time.split(':').map(part => parseInt(part, 10));
                  let seconds = 0;
                  if (parts.length === 3) {
                    // 時:分:秒
                    seconds = parts[0] * 3600 + parts[1] * 60 + parts[2];
                  } else if (parts.length === 2) {
                    // 分:秒
                    seconds = parts[0] * 60 + parts[1];
                  }
                  return seconds;
                }


                const shuffledExtractions = shuffle(extractions);
                let currentIndex = 0;
                let songDuration = 0; // songDuration をplayNext関数の外で定義(=グローバルスコープで定義する)


                function fadeOut(audio, duration) {
                  const step = audio.volume / (duration * 100);
                  const fade = setInterval(() => {
                    if (audio.volume > step) {
                      audio.volume -= step;
                    } else {
                      clearInterval(fade);
                      audio.volume = 0;
                      playNext();

                    }
                  }, 10); // 10ミリ秒ごとに音量を減少させる
                }



                function fadeIn(audio, duration) {
                  audio.volume = 0;
                  const step = 1 / (duration * 100);
                  const fade = setInterval(() => {
                    if (audio.volume < 1 - step) {
                      audio.volume += step;
                    } else {
                      clearInterval(fade);
                      audio.volume = 1;
                    }
                  }, 10); // 10ミリ秒ごとに音量を増加させる
                }


                function playNext() {
                  if (currentIndex < shuffledExtractions.length) {
                    const extraction = shuffledExtractions[currentIndex];
                    console.log('test');

                    audioPlayer.src = `{{ url('/playlist/play') }}/${extraction.id}`;
                    console.log('Audio source set to:', audioPlayer.src);

                    audioPlayer.volume = currentIndex === 0 ? 1 : 0; // 1曲目は音量を最大に設定、2曲目以降は0に設定（フェードイン用）
                    audioPlayer.play();
                    console.log('test2');


                    const endSeconds = timeToSeconds(extraction.end);
                    const startSeconds = timeToSeconds(extraction.start);
                    songDuration = endSeconds - startSeconds; // songDuration を更新

                    if (currentIndex !== 0) {
                      fadeIn(audioPlayer, fadeDuration); // 2曲目以降はフェードインを開始
                    }

                    currentIndex++;
                  } else {
                    playButton.style.display = 'block'; // 全ての再生が終わったら再生ボタンを表示する
                  }
                }

                //フェードアウトの要点
                audioPlayer.addEventListener('play', () => {
                  fadeOutTimeout = setTimeout(() => fadeOut(audioPlayer, fadeDuration), (songDuration - fadeDuration) * 1000);

                });

                // 再生バーの更新
                audioPlayer.addEventListener('timeupdate', () => {
                  const progress = (audioPlayer.currentTime / songDuration) * 100;
                  const offset = 283 - (progress / 100) * 283;

                  progressCircle.style.strokeDashoffset = offset;
                });

                audioPlayer.addEventListener('ended', () => {
                  progressCircle.style.strokeDashoffset = 0;
                });

                // ボタンがクリックされたときに再生を開始
                playButton.addEventListener('click', () => {
                  playButton.style.transition = 'opacity 0.5s ease'; // トランジションを設定
                  playButton.style.opacity = '0'; // 透明度を0にする
                  setTimeout(() => {
                    playButton.style.display = 'none'; // 透明度が0になった後に非表示にする
                  }, 500); // トランジションの時間と同じ500ミリ秒後に非表示にする
                  playNext();
                });

                audioPlayer.addEventListener('ended', () => {
                  if (currentIndex >= shuffledExtractions.length) {
                    playButton.style.display = 'block'; // 全ての再生が終わったら再生ボタンを表示する
                  }
                });
              });
            </script>
          @endif
        </div>
      </div>
    </div>
  </div>
</x-app-layout>