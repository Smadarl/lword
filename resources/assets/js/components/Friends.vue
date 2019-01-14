
<template>
    <div>
        <div class="card-header">
            <h2>Friends</h2>
        </div>
        <div class="card-body">
            <ul>
                <li v-for="friend in this.friend_list" :key="friend.friend_id">
                    <a href="#" @click.prevent="selectFriend(friend)">{{friend.friend_name}}</a>
                </li>
            </ul>
            <div v-if="this.selectedFriend">
                <h3>Game list with {{this.selectedFriend.friend_name}}</h3>
                <ul>
                    <li v-for="game in game_list" :key="game.game_id">
                        <a href="#" @click.prevent="selectGame(game.game_id)">Last Played: {{ game.updated_at }}, Guesses: {{ game.turn }}</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            friend_list: [],
            selectedFriend: null,
            game_list: []
        }
    },
    mounted() {
        axios
            .get('/api/user/friends')
            .then(response => {
                this.friend_list = response.data;
            })
            .catch(error => {});
    },
    methods: {
        selectFriend(friend) {
            this.selectedFriend = friend;
            axios
                .get('/api/friend/' + this.selectedFriend.friend_id + '/games')
                .then(response => {
                    console.log(response);
                    this.game_list = response.data.game_list;
                })
                .catch(error => {});
        },
        selectGame(gameId) {

        }
    }
};
</script>