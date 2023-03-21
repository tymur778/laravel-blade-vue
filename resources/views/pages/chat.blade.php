@extends('layout.layout')
@section('content')
    <div id="chat" class="text-sm whitespace-pre-line"></div>

    <textarea id="prompt"
              name="prompt"
              rows="2"
              placeholder="Ask anything: about me, about my website, about whatever"
              class="mt-6 bg-gray-50 border border-gray-300 text-gray-900 disabled:opacity-25 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"></textarea>

    <button type="button" id="send_prompt" onclick="ask();" class="text-gray-900 block mx-auto sm:float-right mt-6 bg-white disabled:opacity-25 border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium text-sm px-10 sm:px-5 py-2.5 sm:py-2.5 mb-2 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">Enter</button>

    <script>
        var chatMaxLength = 4;
        var textarea = document.getElementById("prompt");
        var chat = document.getElementById("chat");

        function ask() {
            if (!textarea.value) {
                return false;
            }

            clearChat();

            var askButton = document.getElementById("send_prompt");
            var eventSource = new EventSource("/chat_query?prompt=" + textarea.value);
            textarea.disabled = true;
            askButton.disabled = true;

            var questionBlock = document.createElement('p');
            questionBlock.classList.add('mb-4', 'bg-gray-300', 'question');
            questionBlock.innerHTML = textarea.value;

            var answerBlock = document.createElement('p');
            answerBlock.classList.add('mb-4', 'answer');

            chat.appendChild(questionBlock);
            chat.appendChild(answerBlock);

            eventSource.onmessage = function (e) {
                if (e.data == "[DONE]") {
                    answerBlock.innerHTML += "";
                    eventSource.close();
                    textarea.value = '';
                    textarea.disabled = false;
                    askButton.disabled = false;
                    setTimeout(() => {textarea.focus()},100);
                } else {
                    var answer = JSON.parse(e.data);

                    if (answer.choices[0].delta.content !== undefined) {
                        answerBlock.innerHTML += answer.choices[0].delta.content;
                    }
                }
            };

            eventSource.onerror = function () {
                answerBlock.innerHTML = 'OpenAI API is currently not working. Try again in a moment.'
                textarea.disabled = false;
                askButton.disabled = false;
            };
        }

        function clearChat() {
            var messageCount = chat.querySelectorAll("p").length;

            if (messageCount > chatMaxLength) {
                var message = chat.querySelector("p");
                message.remove();
                clearChat();
            }
        }

        textarea.addEventListener("keydown", function(event) {
            if (event.key === "Enter") {
                event.preventDefault();
                ask();
            }
        });
    </script>
@endsection
