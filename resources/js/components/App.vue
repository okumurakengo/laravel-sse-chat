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
            <div class="chat">
            </div>
        </div>
        <form>
            <input>
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
            }
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
