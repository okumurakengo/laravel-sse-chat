<template>
    <div id="app" class="container">
        <div class="flex">
            <div class="users">
                <select v-model="selectUser">
                    <option v-for="user in users">
                        {{ user }}
                    </option>
                </select>
            </div>
            <div class="chat" ref="chat">
                 <div v-for="({ user, post, created_at }) in posts">
                    <p><strong>{{ user }}</strong>&nbsp;<small>{{ created_at }}</small></p>
                    <p>{{ post }}</p>
                    <hr>
                </div>
            </div>
        </div>
        <form @submit.prevent="addPost">
            <input v-model="textValue">
            <input type="submit" value="送信">
        </form>
    </div>
</template>

<script>
    const users = ['Bob', 'Alice', 'Carol'];
    export default {
        data() {
            return {
                users,
                selectUser: users[0],
                textValue: '',
                posts: [],
            }
        },
        created() {
            const es = new EventSource('/api/chat/event');
            es.addEventListener('message', e => {
                const { posts } = JSON.parse(e.data)
                if (posts.length) {
                    this.renderList(posts)
                }
            });
        },
        methods: {
            async addPost() {
                if (!this.textValue.trim()) {
                    return
                }
                await axios.post('/api/chat/add', { user: this.selectUser, post: this.textValue })
                this.textValue = ''
            },
            renderList(posts) {
                this.posts = [...this.posts, ...posts]
                // 下に追加したのでスクロールする
                this.$nextTick(() => this.$refs.chat.scrollTop = this.$refs.chat.scrollHeight)
            },
        },
    }
</script>

<style lang="scss" scoped>
#app {
    font-family: Verdana;

    &.container {
        width: 500px;
        padding: 10px;
        background: #eee;

        .users {
            width: 30%;
            height: 300px;
            border-right: 1px solid gray;
        }

        .chat {
            width: 70%;
            height: 300px;
            padding: 10px;
            overflow: scroll;

            p {
                margin: 0;
            }
        }
    }

    .flex {
        display: flex;
    }
}
</style>
