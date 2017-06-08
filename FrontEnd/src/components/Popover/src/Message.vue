<template>
  <ul class="message">
    <li class="message__item" v-for="message in messages">
      <img class="message__avatar img-circle pull-left" src="/static/avatar.png" alt="">
      <div class="message__content">
        <div class="message__title">{{ `${message.name} ${message.title}` }}</div>
        <div class="message__body">{{ message.body }}</div>
        <div class="message__footer">
          <span class="message__date">{{ message.cdate }}</span>
          <router-link class="message__link pull-right" :to="message.link">去处理</router-link>
        </div>
      </div>
    </li>
  </ul>
</template>

<script>
export default {
  name: 'PopoverMessage',
  props: ['messages'],
  data() {
    return {
    }
  },
}
</script>

<style lang="scss" scoped>
@import "~styles/exports";

.message {
  width: 300px;
  margin: 0;
  padding-left: 0;
  list-style: none;

  &__item {
    @include clearfix();
    margin-bottom: 10px;
  }

  &__avatar {
    width: 3em;
  }

  &__content {
    margin-left: 42px;
  }

  &__title {
    @include ellipsis();
    font-size: 13px;
    color: $black-extra-light;
  }

  &__body {
    margin: 4px 0;
    color: $silver;
  }

  &__date {
    color: $silver-light;
  }

  &__link {
    color: $blue-light;
  }
}
</style>
