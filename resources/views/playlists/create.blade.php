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

    .rotate-90 {
      transform: rotate(90deg);
    }
  </style>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
          @if ($extractions->isEmpty())
            <p>このアカウントのプレイリストが作成されていません。</p>
            <p>「アップロード」を使って、曲をライブラリにUPして、</p>
            <p>「ライブラリ」を使って、プレイリストを作成してください。</p>
          @else
            <div class="relative flex justify-center items-center flex-col">
              
              <!-- 青満月再生バーを円形に変更 -->
              <div class="relative w-96 h-96" style="transform: rotate(-90deg);">
                <svg class="absolute top-0 left-0 w-full h-full" viewBox="-20 -20 140 140" >
                  <circle cx="50" cy="50" r="45" class="background-circle" />
                  <circle id="progressCircle" cx="50" cy="50" r="45" class="progress-bar" stroke-linecap="round"/>
                  <image href="{{ asset('storage/images/tsuki.png') }}" x="6" y="6" style="width: 5.5rem;height: 5.5rem;" />
                </svg>
              

                <!-- 青満月再生ボタン -->
                <button id="playButton" class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 hover:opacity-80 text-white font-bold py-2 px-4 rounded-full flex items-center justify-center">
                  <svg id="playIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="0.4" stroke-linecap="round" stroke-linejoin="round" class="w-72 h-72 neon-icon rotate-90">
                    <circle cx="12" cy="12" r="10"/>
                    <polygon points="10 8 16 12 10 16 10 8"/>
                  </svg>
                  <svg id="pauseIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="0.4" stroke-linecap="round" stroke-linejoin="round" class="w-72 h-72 neon-icon hidden">
                     <circle cx="12" cy="12" r="10"/>
                      <line x1="10" y1="8" x2="10" y2="16"/>
                      <line x1="14" y1="8" x2="14" y2="16"/>
                  </svg>
                </button>
            </div>

              <!-- airhorn音 -->
              <audio id="airhorn" src="{{ asset('storage/music/airhorn.mp3') }}" preload="auto"></audio>
         

              <!-- airhorn音用再生ボタン -->
              <button id="airhornButton">
                📯
              </button>
            </div>


            <script>
              document.addEventListener('DOMContentLoaded', function() {
                const extractions = @json($extractions);
                const playButton = document.getElementById('playButton');
                const progressCircle = document.getElementById('progressCircle');
                const fadeDuration = 5; // フェードイン・フェードアウトの時間（秒）
                const airhorn = document.getElementById('airhorn');
                const airhornButton = document.getElementById('airhornButton');
                
                const playIcon = document.getElementById('playIcon');
                const pauseIcon = document.getElementById('pauseIcon');

                let audioContext;
                let currentSource;
                let currentIndex = 0;
                let songDuration = 0;
                
                let isPlaying = false;
                let starttime = 0;
                let elapsedTime = 0;


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

                //---------------------------
                //WebAudioAPIを使わないと連続再生、フェードイン・アウトができなかった
                //audioタグは1曲再生しかできなかった
                //---------------------------
                function fadeIn(audioContext, gainNode, duration) {
                  gainNode.gain.setValueAtTime(0, audioContext.currentTime);
                  gainNode.gain.linearRampToValueAtTime(1, audioContext.currentTime + duration);
                }
                
                function fadeOut(audioContext, gainNode, duration) {
                  gainNode.gain.setValueAtTime(1, audioContext.currentTime);
                  gainNode.gain.linearRampToValueAtTime(0, audioContext.currentTime + duration);
                }

                function playNext() {
                  if (currentIndex < shuffledExtractions.length) {
                    console.log('currentIndex:', currentIndex);// 現在のインデックスが1ずつ増えないと無限ループしてしまう？
                    console.log('shuffledExtractions.length:', shuffledExtractions.length);
                    
                    const extraction = shuffledExtractions[currentIndex];
                    const startSeconds = timeToSeconds(extraction.start);
                    const endSeconds = timeToSeconds(extraction.end);
                    songDuration = endSeconds - startSeconds;

                    // 曲再生の条件をチェック（そのアカウントがUPした曲だけ再生可能）
              fetch(`{{ url('/playlist/check') }}/${extraction.upload_id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.allowed) {
                    
                    fetch(`{{ url('/playlist/play') }}/${extraction.id}`)
                      .then(response => response.arrayBuffer())
                      .then(data => {
                        if (!audioContext) {
                          audioContext = new (window.AudioContext || window.webkitAudioContext)();
                        }
                        audioContext.decodeAudioData(data, buffer => {
                          

                          // gainNodeの初期化
                          currentSource = audioContext.createBufferSource();
                          gainNode = audioContext.createGain();
                          currentSource.buffer = buffer;
                          currentSource.connect(gainNode).connect(audioContext.destination);
                          
                          //startメソッド：オーディオバッファソースの再生を開始するためのもの
                          currentSource.start(0, startSeconds, songDuration);

                          // 再生0秒でfadeDuration秒間のフェードインを開始
                          fadeIn(audioContext, gainNode, fadeDuration);

                          //gainNodeを初期化した後にフェードアウト関数を呼び出さないと機能しない
                          // (songDuration - fadeDuration)秒後にfadeDuration秒間のフェードアウトを開始
                          setTimeout(() => {
                            fadeOut(audioContext, gainNode, fadeDuration);
                          }, (songDuration - fadeDuration) * 1000);

                          currentSource.onended = () => {
                            currentIndex++;

                            playNext();
                          };

                         // プログレスバーの更新
                          const startTime = audioContext.currentTime;
                          function updateProgress() {
                            const elapsedTime = audioContext.currentTime - startTime;
                            const progress = Math.min(elapsedTime / songDuration, 1);
                            const offset = 283 - (progress * 283);
                            progressCircle.style.strokeDashoffset = offset;
                            if (progress < 1) {
                              requestAnimationFrame(updateProgress);
                            }
                          }
                          updateProgress();
                        });
                      });
                  } else {
                        console.log('曲を再生するには、このアカウントで曲をUPしてください。');
                    }
                });
        } else {
            playButton.style.display = 'block'; // 全ての再生が終わったら再生ボタンを表示する
        }
    }

                playButton.addEventListener('click', () => {
                  if(isPlaying){
                    //一時停止
                    currentSource.stop();
                    elapsedTime += audioContext.currentTime - startTime;
                    isPlaying = false;
                    playIcon.classList.remove('hidden');
                    pauseIcon.classList.add('hidden');                 
                  } else {
                    //再生
                    playButton.style.transition = 'opacity 0.5s ease'; // トランジションを設定
                    playButton.style.opacity = '0'; // 透明度を0にする
                    setTimeout(() => {
                    playButton.style.display = 'none'; // 透明度が0になった後に非表示にする
                    }, 500); // トランジションの時間と同じ500ミリ秒後に非表示にする
                    playNext();
                    isPlaying = true;
                    playIcon.classList.add('hidden');
                    pauseIcon.classList.remove('hidden');
                  }
                });


                airhornButton.addEventListener('click', () => {
                  playCount = 0;
                  playAirhorn();
                });

                function playAirhorn(){
                  if (playCount < 1){
                    airhorn.currentTime = 0;
                    airhorn.play();
                    setTimeout(() => {
                      airhorn.pause();
                      playCount++;
                      playAirhorn();
                    },800);//1回目：一瞬だけ再生（800ミリ秒）
                  } else {
                    airhorn.currentTime = 0;
                    airhorn.play();//最後：フル再生
                  }
                }
              });
            </script>
          @endif
        </div>
      </div>
    </div>
  </div>
</x-app-layout>