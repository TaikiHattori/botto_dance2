<x-app-layout>
  <x-slot name="header">
  </x-slot>

  <style>
  #form {
    color: white;
  }

  input {
    color:black
  }
  </style>

  <div id="form" class="px-4 flex justify-center items-center" style="min-height: 50vh;">
    <form action="" method="POST">
      @csrf
      <br>
      <br>
      <br>
        
      <p>タイトル *</p>
      <input type="text" required> 
      
      <p>姓 *</p>
      <input type="text" required> 
      
      <p>名 *</p>
      <input type="text" required> 
      
      <p>メールアドレス *</p>
      <input type="email" required> 

      <p>電話番号</p>
      <input type="tel"> 
      
      <p>内容 *</p>
      <input type="text" required> 

      <button type="submit" class="w-full border-solid border border-white mt-4 hover:opacity-80 text-white font-bold py-2 px-4 rounded">送信</button>

    </form>
  </div>


</x-app-layout>

<script>
    
</script>
