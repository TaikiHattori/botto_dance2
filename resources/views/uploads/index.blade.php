{{-- <div>
    <!-- Knowing is not enough; we must apply. Being willing is not enough; we must do. - Leonardo da Vinci -->
</div> --}}

<!-- resources/views/tweets/index.blade.php -->

<x-app-layout>
  <x-slot name="header">
    <!-- <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      {{ __('アップロード一覧') }}
    </h2> -->
  </x-slot>

<style>
    html, body {
      color: #ffffff; /* テキスト色を白に */
      margin: 0;
      padding: 0;
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

    .getCountId {
      font-size:30px;
    }
  </style>

<div class="py-12 px-4">

  <!-- uploadsテーブルのid数を取得 -->
  <p class="getCountId">Total：{{ $getCountId }}曲</p>

  <br>

  <form action="{{ route('uploads.bulkDelete') }}" method="post" onsubmit="updateDeleteForm(); return confirm('本当に削除しますか？');" class="hidden" id="delete-form">
    @csrf
    @method('DELETE')
    <div id="hidden-input-container"></div><!-- 隠しフィールドを追加するためのコンテナ -->
    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">削除</button>
  </form>

  <br>

  <!-- 全てのチェックボックスをチェックするボタン -->
   <button onclick="checkAllCheckboxes()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4">全て選択</button>

  @foreach ($uploads as $upload)
  <div class="flex max-w-md mx-auto overflow-hidden rounded-lg shadow-lg mb-4" style="box-shadow: 0px 0px 30px 10px rgb(255 255 255 / 80%);">
    <div class="w-1/3 bg-no-repeat bg-contain bg-center" style="background-image: url('{{ asset('storage/images/tsuki2.png') }}')" onclick="toggleCheckbox({{ $upload->id }})">
      <input type="checkbox" name="upload[]" value="{{ $upload->id }}" id="checkbox-{{ $upload->id }}" class="hidden" onchange="toggleDeleteButton()">
    </div>

    <div class="w-2/3 p-4 md:p-4">
        <p class="text-xm font-bold text-white">{{ $upload->title }}</p>

        <div class="flex justify-between mt-3 item-center">
            <a href="{{ route('uploads.show', $upload) }}" class="text-sm hover:text-gray-200">詳細を見る</a>
            <a href="{{ route('extractions.create', ['upload_id' => $upload->id]) }}" class="border-solid border border-white px-2 py-1 font-bold text-white uppercase transition-colors duration-300 transform rounded hover:opacity-80 focus:outline-none">抽出</a>
        </div>
    </div>
  </div>
  @endforeach
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

    function updateDeleteForm() {
        const checkboxes = document.querySelectorAll('input[type="checkbox"]');
        const deleteForm = document.getElementById('delete-form');
        const hiddenInputContainer = document.getElementById('hidden-input-container');
        hiddenInputContainer.innerHTML = ''; // 既存の隠しフィールドをクリア※重複防止＆最新データを反映しないと変な挙動になる場合がある

        checkboxes.forEach((checkbox) => {
            if (checkbox.checked) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'uploads[]';
                hiddenInput.value = checkbox.value;
                hiddenInputContainer.appendChild(hiddenInput);// チェックされたチェックボックスの値を隠しフィールドとして追加
            }
        });
    }

    function checkAllCheckboxes(){
      const checkboxes = document.querySelectorAll('input[type="checkbox"]');
      const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
      
      checkboxes.forEach((checkbox) => {
        checkbox.checked = !allChecked;
        const container = checkbox.parentElement;
        if(checkbox.checked){
          //!allCheckedの場合（チェックされていない場合）
            container.classList.add('checked');
        } else {
          //allCheckedの場合（チェックされている場合）
            container.classList.remove('checked');
        }
      });
      toggleDeleteButton();
    }
    
  </script>
</x-app-layout>