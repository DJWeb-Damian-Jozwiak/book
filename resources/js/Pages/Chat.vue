<script setup>
import {ref} from "vue";

const socket = new WebSocket('ws://localhost:8080');
const my_message = ref('')
const bot_writing = ref(false)
const messages = ref([
  {type: 'Asistant', content: 'Ask me anything'},
])
const connectionOpened = ref(false)
socket.addEventListener('open', function (event) {
  setTimeout(() => {
    connectionOpened.value = true
  }, 100)
});
const checkConnection = () => {
  if(!connectionOpened.value) {
    return;
  }
  if (socket.readyState !== WebSocket.OPEN) {
    messages.value.push({
      type: 'Asistant',
      content: 'Connection lost. Please refresh the page.'
    })
    bot_writing.value = false
    clearInterval(pingInterval)
  }
}
const pingInterval = setInterval(checkConnection, 1000)

const sendUserMessage = message => {
  socket.send(JSON.stringify({
    event: 'message',
    data: {
      message: message
    }
  }))
  messages.value.push({type: 'user', content: message})
  my_message.value = '';
  bot_writing.value = true;
}

const sendMessage = () => {
  sendUserMessage(my_message.value)
}

socket.onmessage = function (event) {
  if (typeof event.data === 'string') {
    const decoded = JSON.parse(event.data)

    if (decoded.event === 'message') {
      messages.value.push({type: 'Asistant', content: decoded.data})
      bot_writing.value = false
    }
  }
};
</script>

<template>
  <div class="container d-flex flex-column flex-grow-1">
    <main class="flex-grow-1">
      <div class="d-flex h-100 justify-content-end align-items-end flex-grow-1">
        <div class="card chat-box col-12 mx-auto mt-3">
          <div class="card-body p-3 d-flex flex-column">
            <template v-for="message in messages">
              <div class="d-flex flex-row align-items-center message-parent" :class="message.type">
                <div v-html="message.content.replaceAll('\n', '<br>')"  class="message" :class="message.type">
                </div>
              </div>
            </template>
            <div class="writing-dots" v-if="bot_writing">
              <div class="dot" id="dot1"></div>
              <div class="dot" id="dot2"></div>
              <div class="dot" id="dot3"></div>
            </div>
          </div>
          <div class="card-footer">
            <div class="d-flex">
              <input @keyup.enter="sendMessage" :disabled="bot_writing" class="form-control"
                     v-model="my_message">
              <button style="border: none" :disabled="bot_writing" class="btn">
                Send Message
              </button>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<style scoped>
.writing-dots {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100px;
  height: 50px;
  background-color: lightgrey;
  border-radius: 25px;
  padding: 10px;
}
.dot {
  width: 8px;
  height: 8px;
  background-color: black;
  border-radius: 50%;
  margin: 0 2px;
  animation: dot-jump 1.4s infinite;
}

@keyframes dot-jump {
  0%, 80%, 100% {
    transform: translateY(0);
  }
  40% {
    transform: translateY(-10px);
  }
}

#dot1 {
  animation-delay: 0s;
}

#dot2 {
  animation-delay: 0.2s;
}

#dot3 {
  animation-delay: 0.4s;
}

.spinner-border {
  width: 3rem;
  height: 3rem;
}
.icon {
  height: 40px;
  cursor: pointer;
}
.message-parent {
  padding: 10px;
  width: 85%;
  &.user {
    align-self: end;
    justify-content: end;
  }

  &.Asistant {
    align-self: start;
  }
}
.tile {
  border: 2px solid white;
  padding: 10px;
  margin: 10px;
  width: 250px;
  text-align: center;
  border-radius: 10px;
  box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s;
  &.clickable {
    cursor: pointer;
    background: white;
    border: 2px solid blue;
    &:hover {
      transform: scale(1.05);
    }
  }
}
.message {
  padding: 10px;
  border-radius: 20px;
  display: flex;
  margin: 10px 0;

  &.user {
    align-self: end;
    background-color: #0dcaf0;
    text-align: right;
  }

  &.Asistant {
    background-color: lightgrey;
    text-align: left;
  }
}
</style>