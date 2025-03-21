<x-app-layout>
  <x-slot name="header">
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
      background-color: #333333 !important; /* ボタンの背景色を黒っぽく */
    }

    .bg-gray-100 {
      background-color: #1f1f1f !important; /* 背景色を黒っぽく */
    }

    #dropzone-file {
      opacity: 0;
      width: 300px;
    }

    /* id属性を指定 */
    #genre-select {
        width: 300px;
        color: black;
    }

    #genre-select option {
        color: black;
    }

    #genre-input {
        color: black;
    }
  </style>

<div class="px-4 flex justify-center items-center" style="min-height: 50vh;">
  <form action="{{ route('uploads.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <label for="dropzone-file" class="flex flex-col items-center w-full max-w-lg p-12 mx-auto mt-2 text-center border-2 border-gray-300 border-dashed cursor-pointer dark:bg-gray-900 dark:border-gray-700 rounded-xl" style="box-shadow: 0px 0px 30px 10px rgb(255 255 255 / 80%);">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-white">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
        </svg>

        <h2 class="mt-1 tracking-wide text-white dark:text-gray-200">Music files</h2>
        <p class="mt-2 tracking-wide text-white dark:text-gray-400">Upload your file mp3. </p>

        @error('upload')
        <span class="text-red-500 text-xs italic">{{ $message }}</span>
        @enderror

        <input id="dropzone-file" type="file" name="files[]" multiple required onchange="updateFileNames(this)"/>
        <ul id="file-names" class="text-white"></ul> <!-- アップロードファイル名を表示するための要素 -->

        <br>
        <!-- ジャンル選択または入力 -->
        <div>
            <label  class="block text-white">
              ジャンルを選択または入力（任意）：
            </label>
              <select id="genre-select" name="genreSelect" class="block w-full mt-1">
                <option value="">選択</option>
                <option value="ヒップホップ">ヒップホップ</option>
                <option value="ロック">ロック</option>
                <option value="ポップ">ポップ</option>
                <option value="アニソン">アニソン</option>
              </select>
              <input type="text" id="genre-input" name="genreInput" class="block w-full mt-2" placeholder="または入力">
    </label>
        </div>
    <button type="submit" class="w-full border-solid border border-white mt-4 hover:opacity-80 text-white font-bold py-2 px-4 rounded">アップロード</button>

  </form>
</div>

@if (session('error'))
    <script>
        alert('{{ session('error') }}');
    </script>
@endif

</x-app-layout>

<script>
    function updateFileNames(input) {
      const fileNamesList = document.getElementById('file-names');

      Array.from(input.files).forEach(file => {
        const li = document.createElement('li');
        li.textContent = `【 ${file.name} 】`;
        fileNamesList.appendChild(li);
      });
    }

    // ジャンル選択または入力の相互排他処理
    document.getElementById('genre-select').addEventListener('change', function() {
        if(this.value) {
            document.getElementById('genre-input').value = '';
        } 
    });

    document.getElementById('genre-input').addEventListener('input', function() {
        if(this.value) {
            document.getElementById('genre-select').value = '';
        }
    });
</script>
