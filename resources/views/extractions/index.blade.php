<div>
    <!-- You must be the change you wish to see in the world. - Mahatma Gandhi -->
</div>


<x-app-layout>
  <x-slot name="header">
    <!-- <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      {{ __('抽出一覧') }}
    </h2> -->
  </x-slot>

<style>
    html, body {
      color: #ffffff; /* テキスト色を白に */
      margin: 0;
      padding: 0;
      height: 100%;
    }

    .text-gray-800, .text-gray-900, .dark\:text-gray-100 {
      color: #ffffff !important; /* テキスト色を白に */
    }

    .bg-blue-500 {
      background-color: #333333 !important; /* ボタンの背景色を#333333に設定 */
    }

    .hover\:bg-blue-700:hover {
      background-color: #444444 !important; /* ボタンのホバー時の背景色を#444444に設定 */
    }
    .container {
      min-height: 100vh; /* コンテナの高さを画面全体に */
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }

    .min-h-screen {
      min-height: 100vh; /* 画面全体の高さを確保 */
    }

    .bg-gray-100 {
      background-color: #1f1f1f !important; /* 背景色を#1f1f1fに設定 */
      border: 1px solid #ffffff !important; /* 枠線の色を白に設定 */
    }

    .dark\:bg-gray-900 {
      background-color: #1f1f1f !important; /* ダークモードの背景色を#1f1f1fに設定 */
    }

    .text-blue-500 {
      color: #1e90ff !important; /* リンクのテキスト色を青に設定 */
    }

    .hover\:text-blue-700:hover {
      color: #1c86ee !important; /* リンクのホバー時のテキスト色を濃い青に設定 */
    }

    .checked {
        border: 6px solid #1c86ee; /* チェックされたときのスタイル */
    }
  </style>

  <div class="py-12 px-4">
    <form id="bulkDelete-extractions" action="{{ route('extractions.bulkDelete') }}" method="POST" onsubmit="return confirm('本当に削除しますか？');">
        @csrf
        @method('DELETE')  

    @foreach ($extractions as $extraction)
    <div class="flex max-w-md mx-auto overflow-hidden rounded-lg shadow-lg mb-4" style="box-shadow: 0px 0px 30px 10px rgb(255 255 255 / 80%);">
        <div class="w-1/3  bg-no-repeat bg-contain bg-center" style="background-image: url('{{ asset('storage/images/tsuki2.png') }}')" onclick="toggleCheckbox({{ $extraction->id }})">
            <input type="checkbox" name="extractions[]" value="{{ $extraction->id }}" id="checkbox-{{ $extraction->id }}" class="hidden" onchange="toggleDeleteButton()">
        </div>
    
        <div class="w-2/3 p-4 md:p-4">
            <p class="text-xm font-bold text-white">{{ $extraction->upload->title }}</p>
            <p class="text-white text-sm">開始: {{ substr($extraction->start, 3) }}</p>
            <p class="text-white text-sm">終了: {{ substr($extraction->end, 3) }}</p>

            <div class="flex justify-between mt-3 item-center">
                <a href="{{ route('extractions.show', $extraction) }}" class="text-sm hover:text-gray-200">詳細を見る</a>
              </div>
        </div>
    </div>
    @endforeach

    </form>
  </div>

  <script>
    function toggleCheckbox(id) {
        const checkbox = document.getElementById('checkbox-' + id);
        const container = checkbox.parentElement;
        
        checkbox.checked = !checkbox.checked;//論理否定演算

        if (checkbox.checked) {
            container.classList.add('checked');
        } else {
            container.classList.remove('checked');
        }
        toggleDeleteButton();
    }

    function toggleDeleteButton() {
        const checkboxes = document.querySelectorAll('input[type="checkbox"]');
        const deleteForm = document.getElementById('delete-form');
        let ischecked = false;
        
        checkboxes.forEach((checkbox) => {
            if (checkbox.checked) {
                ischecked = true;
            }
        });

        if (ischecked) {
            deleteForm.classList.remove('hidden');
        } else {
            deleteForm.classList.add('hidden');
        }
    }
  </script>
</x-app-layout>