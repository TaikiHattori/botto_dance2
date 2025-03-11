<x-app-layout>
  <x-slot name="header">
  </x-slot>

  <style>

  </style>

<div class="px-4 flex justify-center items-center" style="min-height: 50vh;">
  <form action="" method="POST">
    @csrf

    <h1>お問い合わせ</h1>
    
    <p>タイトル</p>
    <input type=""> 
    <input type="">
    <input type="">
    <input type="">
    <input type="">
    <input type="">
        
    <button type="submit" class="w-full border-solid border border-white mt-4 hover:opacity-80 text-white font-bold py-2 px-4 rounded">送信</button>

  </form>
</div>


</x-app-layout>

<script>
    
</script>
