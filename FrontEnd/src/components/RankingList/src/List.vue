<template>
  <div class="ranking__box">
    <span class="ranking__extra">{{ data.list.length }} 篇</span>
    <h3 class="ranking__title">{{ data.title }}</h3>
    <ul class="ranking__list">
      <li class="ranking__item" v-for="item in data.list">
        <span class="ranking__dot"></span>
        <div class="ranking__article">
          <h4 class="ranking__article-title">{{ item.title }}</h4>
        </div>
        <a class="ranking__url" href="#">查看详情</a>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  name: 'RankingList',
  props: ['data'],
}
</script>

<style lang="scss" scoped>
@import "~styles/exports";

.ranking {
  &__box {
    padding: 15px;
    border: 1px solid $gray-light;
    border-radius: 6px;
  }

  &__title {
    margin: 0;
    font-size: 16px;
  }

  &__extra {
    float: right;
    font-size: 14px;
  }

  &__list {
    padding-left: 0;
    list-style: none;
  }

  &__item {
    position: relative;
    height: 32px;
    line-height: 32px;
    @include clearfix();
  }

  &__dot,
  &__article,
  &__url {
    float: left;
  }

  &__dot {
    position: absolute;
    top: 14px;
    display: block;
    width: 4px;
    height: 4px;
    margin-right: -5px;
    border-radius: 50%;
    background: $blue;
  }

  &__article {
    width: 100%;
  }

  &__article-title {
    margin: 0;
    margin-left: 12px;
    margin-right: 50px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    font-size: 14px;
    font-weight: 400;
  }

  &__url {
    margin-left: -48px;
    font-size: 12px;
    color: $blue;
  }
}
</style>
