<div>
    <!-- Be present above all else. - Naval Ravikant -->
</div>

 <x-app-layout>
  <x-slot name="header">
    <!-- <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
      {{ __('編集') }}
    </h2> -->
  </x-slot>
  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
          <a href="{{ route('extractions.show', $extraction) }}" class="text-blue-500 hover:text-blue-700 mr-2">詳細に戻る</a>
          
          <form method="POST" action="{{ route('extractions.update', $extraction) }}">
            @csrf
            @method('PUT')
            
            <div class="mb-4 text-black">
              <p>開始</p>
              <div class="flex">
              <input type="number" name="start_minu" id="start_minu" value="{{ $extraction->start }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="分">
              <input type="number" name="start_sec" id="start_sec" value="{{ $extraction->start }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="秒">
              </div>
            
              <p>終了</p>
              <div class="flex">
              <input type="number" name="end_minu" id="end_minu" value="{{ $extraction->end }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="分">
              <input type="number" name="end_sec" id="end_sec" value="{{ $extraction->end }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-300 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="秒">
              </div>
              
              @error('extraction')
                <span class="text-red-500 text-xs italic">{{ $message }}</span>
              @enderror
            </div>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">編集する</button>
          </form>
        </div>
      </div>
    </div>
  </div>
 </x-app-layout>