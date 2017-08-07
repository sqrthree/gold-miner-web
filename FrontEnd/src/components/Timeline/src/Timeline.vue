<template>
  <ul class="timeline timeline--theme-blue">
    <li class="timeline__item" v-for="item in data">
      <div class="timeline__tail"></div>
      <div class="timeline__head"></div>
      <div class="timeline__content">{{ `${item.user} 于 ${item.time} ${item.action}` }}</div>
    </li>
  </ul>
</template>

<script>
export default {
  name: 'Timeline',
  props: ['data'],
}
</script>

<style lang="scss" scoped>
@import "~styles/exports";

.timeline {
  margin: 0;
  padding-left: 0;

  &__item {
    position: relative;
    padding: 0 0 12px;
    list-style: none;

    &:last-child {
      .timeline__head {
        border-color: #108ee9;
        color: #108ee9;
      }

      .timeline__tail {
        display: none;
      }
    }
  }

  &__tail {
    position: absolute;
    left: 7px;
    top: 0;
    height: 100%;
    border-left: 2px solid #e9e9e9;
  }

  &__head {
    position: absolute;
    width: 12px;
    height: 12px;
    background-color: #fff;
    border-radius: 100px;
    border: 2px solid #e9e9e9;
  }

  &__content {
    padding: 0 0 10px 24px;
    font-size: 12px;
    line-height: 22px;
    position: relative;
    top: -3px;
  }
}
</style>
