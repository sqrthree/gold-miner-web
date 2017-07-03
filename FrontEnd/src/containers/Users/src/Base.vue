<template>
  <div class="container">
    <div class="user-info">
      <div class="user-info__avatar"><img src="/static/images/avatar.png" alt=""></div>
      <h3>根号三</h3>
      <p class="user-info__bio">Full-Stack Developer</p>
    </div>
    <router-view></router-view>
  </div>
</template>

<script>
export default {
  name: 'UserBase',
}
</script>

<style lang="scss" scoped>
.user-info {
  margin-top: 80px;
  margin-bottom: 100px;
  text-align: center;

  &__avatar {

    img {
      width: 80px;
      height: 80px;
      border-radius: 50%;
    }
  }
}
</style>
