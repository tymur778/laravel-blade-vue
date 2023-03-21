<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/inertia-vue3';
</script>

<template>
    <Head :title="$page.props.title" />

    <AppLayout>
        <div class="container max-w-2xl mx-auto mb-auto">
            <div class="text-sm whitespace-pre-line">
                <div v-for="message in chatMessages" :key="message.id" :class="{ 'question bg-gray-300 mb-4': message.isQuestion, 'answer mb-4': !message.isQuestion }">{{ message.content }}</div>
            </div>

            <textarea rows="2"
                      ref="textarea"
                      v-model="prompt"
                      placeholder="Ask anything: about me, about my website, about whatever"
                      @keydown.enter.prevent="sendMessage"
                      :disabled="isDisabled"
                      class="mt-6 bg-gray-50 border border-gray-300 text-gray-900 disabled:opacity-25 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"></textarea>

            <button
                type="button"
                ref="send_prompt"
                @click="sendMessage"
                :disabled="isDisabled"
                class="text-gray-900 block mx-auto sm:float-right mt-6 bg-white disabled:opacity-25 border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium text-sm px-10 sm:px-5 py-2.5 sm:py-2.5 mb-2 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">Enter</button>
        </div>
    </AppLayout>
</template>

<script>
export default {
    name: "Chat",
    data() {
        return {
            messagesCounter: 0,
            chatMaxLength: 4,
            prompt: "",
            answerContent: "",
            chatMessages: [],
            isDisabled: false,
        };
    },
    methods: {
        sendMessage() {
            if (!this.prompt.trim()) {
                return;
            }

            if (this.chatMessages.length > this.chatMaxLength)
                this.chatMessages = this.chatMessages.slice(-this.chatMaxLength);

            const questionBlock = {
                id: this.messagesCounter++,
                content: this.prompt.trim(),
                isQuestion: true
            };

            this.chatMessages.push(questionBlock);

            this.isDisabled = true;

            const eventSource = new EventSource(`/chat_query?prompt=${encodeURIComponent(questionBlock.content)}`);
            this.prompt = '';

            let answerBlock = {
                id: this.messagesCounter++,
                content: '',
                isQuestion: false
            };
            this.chatMessages.push(answerBlock);

            const answerHandler = (e) => {
                if (e.data === "[DONE]") {
                    eventSource.close();
                    this.answerContent = '';
                    this.isDisabled = false;
                    setTimeout(() => {this.$refs.textarea.focus()},100);
                } else {
                    const answer = JSON.parse(e.data);
                    const answerIndex = this.chatMessages.findIndex(msg => msg.id === answerBlock.id);
                    if (answer.choices[0].delta.content !== undefined) {
                        this.chatMessages[answerIndex].content += answer.choices[0].delta.content;
                    }
                }
            };

            eventSource.addEventListener("message", answerHandler);

            eventSource.addEventListener("error", () => {
                this.chatMessages.push({
                    id: Date.now(),
                    content: 'OpenAI API is currently not working. Try again in a moment.',
                    isQuestion: false
                });
                this.isDisabled = false;

                eventSource.close();
            });
        }
    }
};
</script>

<style scoped>

</style>
