<template>
    <div class="card card-default chat-box" >
        <div class="card-header">

        <b :class="{'text-danger':session_block}">{{friend.name}}   <span v-if="session_block">(Engellendi)</span>
        </b>


            <a href="" @click.prevent="close"><i class="fa fa-times float-right" aria-hidden="true"></i></a>
            <div class="dropdown float-right">

                <a href=""  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-ellipsis-v  mr-4" aria-hidden="true"></i>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <a class="dropdown-item" href="#" v-if="session_block"   @click.prevent="unblock">Engelle Kaldır</a>
                        <a class="dropdown-item" href="#" v-else  @click.prevent="block">Engelle</a>

                        <a class="dropdown-item" href="#" @click.prevent="clear">Sohbeti Temizle</a>

                    </div>
                </a>


            </div>

        </div>

        <div class="card-body" v-chat-scroll>
            <div class="row">
                <div class="col-12">

                    <p class="card-text" v-for="chat in chats" :key="chat.message" >

                        {{chat.message}}

                    </p>




                </div>



            </div>

        </div>
        <form class="card-footer" @submit.prevent="send">
            <div class="col-12 form-group">
                <input type="text" class="form-control" placeholder="Sohbete başlamak için bir şeyler yazın.."
                :disabled="session_block">
            </div>

        </form>
    </div>
</template>

<script>


    export default {
        props: ["friend"],
        data() {
            return {
                chats: [],
                message: null,
                isTyping: false
            };
        },
        computed: {
            session() {
                return this.friend.session;
            },
            can() {
                return this.session.blocked_by == auth.id;
            }
        },
        watch: {
            message(value) {
                if (value) {
                    Echo.private(`Chat.${this.friend.session.id}`).whisper("typing", {
                        name: auth.name
                    });
                }
            }
        },
        methods: {
            send() {
                if (this.message) {
                    this.pushToChats(this.message);
                    axios
                        .post(`/send/${this.friend.session.id}`, {
                            content: this.message,
                            to_user: this.friend.id
                        })
                        .then(res => (this.chats[this.chats.length - 1].id = res.data));
                    this.message = null;
                }
            },
            pushToChats(message) {
                this.chats.push({
                    message: message,
                    type: 0,
                    read_at: null,
                    sent_at: "Just Now"
                });
            },
            close() {
                this.$emit("close");
            },
            clear() {
                axios
                    .post(`/session/${this.friend.session.id}/clear`)
                    .then(res => (this.chats = []));
            },
            block() {
                this.session.block = true;
                axios
                    .post(`/session/${this.friend.session.id}/block`)
                    .then(res => (this.session.blocked_by = auth.id));
            },
            unblock() {
                this.session.block = false;
                axios
                    .post(`/session/${this.friend.session.id}/unblock`)
                    .then(res => (this.session.blocked_by = null));
            },
            getAllMessages() {
                axios
                    .post(`/session/${this.friend.session.id}/chats`)
                    .then(res => (this.chats = res.data.data));
            },
            read() {
                axios.post(`/session/${this.friend.session.id}/read`);
            }
        },
        created() {
            this.read();

            this.getAllMessages();

            Echo.private(`Chat.${this.friend.session.id}`).listen(
                "PrivateChatEvent",
                e => {
                    this.friend.session.open ? this.read() : "";
                    this.chats.push({ message: e.content, type: 1, sent_at: "Just Now" });
                }
            );

            Echo.private(`Chat.${this.friend.session.id}`).listen("MsgReadEvent", e =>
                this.chats.forEach(
                    chat => (chat.id == e.chat.id ? (chat.read_at = e.chat.read_at) : "")
                )
            );

            Echo.private(`Chat.${this.friend.session.id}`).listen(
                "BlockEvent",
                e => (this.session.block = e.blocked)
            );

            Echo.private(`Chat.${this.friend.session.id}`).listenForWhisper(
                "typing",
                e => {
                    this.isTyping = true;
                    setTimeout(() => {
                        this.isTyping = false;
                    }, 2000);
                }
            );
        }
    };
</script>

<style scoped>
.chat-box{
    height: 400px;
}
    .card-body{
        overflow-y:scroll ;
    }
</style>