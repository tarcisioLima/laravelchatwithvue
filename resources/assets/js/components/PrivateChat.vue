<template>
    <v-layout row>
        <v-flex class="online-users" xs3>
            <v-list>
                <v-list-tile 
                    v-for="friend in friends" :key="friend.id" @click="setActiveFriend(friend.id)"
                    :color="(friend.id == activeFriend) ? 'green' : ''">
                    <v-list-tile-action>
                        <v-icon color="green">account_circle</v-icon>
                    </v-list-tile-action>
                    <v-list-tile-content>
                        <v-list-tile-title>{{ friend.name }}</v-list-tile-title>
                    </v-list-tile-content>
                </v-list-tile>
            </v-list>
        </v-flex>
        
        <v-flex id="privateMessageBox" class="messages mb-5" xs9>
            
            <v-list class="message-list">
                <v-subheader>Conteúdo chat</v-subheader>
                <v-divider></v-divider>
                <v-list-tile class="p-3" v-for="(message, index) in allMessages" :key="index">
                    <v-layout>
                        <v-flex>
                            <v-layout column>
                                <v-flex>
                                    <span class="small font-italic">{{ message.user.name }}</span>
                                </v-flex>
                                <v-flex>
                                    <v-chip v-if="user" :color="(user.id!==message.user.id)? 'red':'green'" text-color="white">
                                        <v-list-tile-content>{{ message.message }}</v-list-tile-content>
                                    </v-chip>
                                </v-flex>
                                <v-flex class="caption font-italic">
                                    {{ message.created_at }}
                                </v-flex>
                            </v-layout>
                       </v-flex>
                    </v-layout>
                </v-list-tile>
                <p class="mt-3 ml-4" v-if="typingFriend.name">{{ typingFriend.name }} está digitando...</p>
            </v-list>
            
            <v-footer height="auto" fixed color="grey">
                <v-layout row>
                    <v-flex xs6 offset-xs3 justify-center align-center>
                        <v-text-field
                            rows=2
                            v-model="message"
                            label="Envie uma menssagem"
                            single-line
                            @keydown= "onTyping"
                            @keyup.enter="sendMessage">
                        </v-text-field>
                    </v-flex>
                    <v-flex xs2> 
                        <v-btn @click="sendMessage" dark class="mt-3 ml-2 white--text" small color="green">enviar</v-btn>
                    </v-flex>
                </v-layout>
            </v-footer>
        </v-flex>
        
    </v-layout>
</template>

<script>
   export default {
    props:['user'],
    
    data () {
      return {
        message: '',
        activeFriend: null,
        typingFriend: {},
        allMessages: [],
        users: [],
        typingClock: null
      }
    },
    computed: {
        friends() {
            return this.users.filter((user) => {
                return user.id !== this.user.id;
            })
        }
    },
    watch: {
        activeFriend(val) {
            this.typingFriend = {};
            this.fetchMessages();
        }
    },
    methods: {
        sendMessage() {
            if(!this.message){
              return alert('Digite algo');
            }
            if(!this.activeFriend) {
                return alert('Selecione um usuário a quem mandar mensagens!')
            }
              axios.post('/private-messages/' + this.activeFriend, {message: this.message}).then(response => {
                    this.message = '';
                    this.allMessages.push(response.data.message)
                    setTimeout(this.scrollToEnd, 100);
              });
        },
        fetchMessages() {
            if(!this.activeFriend) {
                return alert('Selecione um usuário a quem mandar mensagens!')
            }
            axios.get('/private-messages/' + this.activeFriend).then(response => {
                this.allMessages = response.data;
            });
            
            setTimeout(this.scrollToEnd, 1000);
        },
        fetchUsers() {
            axios.get('/users').then(response => {
                this.users = response.data;
                //load the first friend 
                this.activeFriend = this.friends[0].id;
            });
        },
        onTyping() {
            Echo.private('privatechat.' + this.activeFriend).whisper('typing', {
                user: this.user
            });
        },
        setActiveFriend(friendId) {
            this.activeFriend = friendId;
        },
        scrollToEnd(){
            document.querySelector('.message-list').scrollTop = 99999999;
        }
    },
    
    created() {
      this.fetchUsers();
      
      Echo.private('privatechat.' + this.user.id)
      .listen('PrivateMessageSent', (e) => {
          this.activeFriend = e.message.user_id;
          this.allMessages.push(e.message)
          setTimeout(this.scrollToEnd, 100);
      })
      .listenForWhisper('typing', (e) => {
          //apenas para o amigo ativo
          if(e.user.id == this.activeFriend) {
              this.typingFriend = e.user;
              if(this.typingClock) { clearTimeout(); clearTimeout(this.typingClock); }
              
              this.typingClock = setTimeout(() => {
                this.typingFriend = {};
              }, 4000);
          }
      })
    }
  }
</script>

<style scoped>
    .mb-5 {
        margin-bottom: 100px!important;
    }
    .message-list {
        height: 70vh;
        overflow-y: auto;
    }
</style>