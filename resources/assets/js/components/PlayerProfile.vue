
<template>
    <div>
        <div class="card-header">
            <h2>{{ user.name }}</h2>
        </div>

        <div class="card-body">
            <div>
                <h2>User Information</h2>
                <form @submit.prevent="onSubmit">
                    <label for="maxrecur">Name</label>
                    <input type="text" id="maxrecur" v-model="user.name" />
                    <br/>

                    <label for="maxlength">Email</label>
                    <input type="text" id="maxlength" v-model="user.email" />
                    <br/>

                    <button class="button is-primary">Change Name/Email</button>
                </form>
            </div>

            <div>
                <h2>Change Password</h2>
                <form @submit.prevent="onPasswordChange">
                    <label for="curpw">Current Password</label>
                    <input type="password" id="curpw" v-model="curpw" />
                    <br/>

                    <label for="pw1">Password</label>
                    <input type="password" id="pw1" v-model="pw1" />
                    <br/>

                    <label for="pw2">Confirm</label>
                    <input type="password" id="pw2" v-model="pw2" />
                    <br/>

                    <button class="button is-primary">Change Password</button>
                    </form>
            </div>
        </div>
    </div>
</template>

<script>
export default {
  data() {
    return {
      curName: "",
      user: {
        name: "",
        email: "",
        id: 0
      },
      curpw: "",
      pw1: "",
      pw2: "",
      errors: []
    };
  },
  mounted() {
    axios
      .get("api/user/info")
      .then(response => {
        this.user = Object.assign({}, response.data);
        this.curName = user.name;
      })
      .catch(error => {});
  },
  methods: {
    onPasswordChange() {
      let password = this.curpw;
      if (password.length < 5) {
        this.errors = [
          "Current password must be at least 5 characters: " + password.length
        ];
        return;
      }
      this.curpw = "";
      if (this.pw1.length < 6) {
        this.errors = [
          "New Password must be at least 6 characters: " + this.pw1.length
        ];
        return;
      } else if (this.pw1 != this.pw2) {
        this.errors = ["New Passwords must match"];
        return;
      }
      axios
        .post("api/user/changepw", {
          curPW: password,
          newPassword: this.pw1,
          newPassword_confirmation: this.pw2
        })
        .then(response => {
          // response.data.message should be displayed somewhere.
        })
        .catch(error => {
          this.errors = [error.errors];
        });
    }
  }
};
</script>
