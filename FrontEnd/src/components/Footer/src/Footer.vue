<template>
  <footer class="footer">
    <ul class="footer__list container">
      <li class="footer__item">
        <h2 class="footer__title">GitHub</h2>
        <div class="footer__body"><a target="_blank" href="https://github.com/fanyijihua/gold-miner-web"><span>仓库地址</span></a></div>
      </li>
      <li class="footer__item">
        <h2 class="footer__title"><span>相关站点</span></h2>
        <div class="footer__body"><a href="https://juejin.im/">掘金</a><span> - </span><span>一个只有高手分享的社区</span></div>
        <div class="footer__body"><a href="https://github.com/xitu/gold-miner/">掘金翻译计划</a><span> - </span><span> GitHub 仓库</span></div>
      </li>
      <li class="footer__item">
        <h2 class="footer__title"><span>社区</span></h2>
        <div class="footer__body"><a href="https://github.com/fanyijihua/gold-miner-web/issues/new?title=[%E6%84%8F%E8%A7%81%E5%8F%8D%E9%A6%88]"><span>意见反馈</span></a></div>
        <div class="footer__body"><a href="https://github.com/fanyijihua/gold-miner-web/issues/new"><span>报告 bug</span></a></div>
      </li>
      <li class="footer__item">
        <h2 class="footer__title">Copyright © 2017 掘金</h2>
        <div class="footer__body"><span>掘金翻译计划出品 · 社区驱动</span></div>
        <div class="footer__body"><a href="https://github.com/leviding">LeviDing</a><span> - </span><span>翻译计划管理员</span></div>
        <div class="footer__body"><a href="https://github.com/fanyijihua/gold-miner-web/graphs/contributors">Contributors</a><span> - </span><span>贡献者列表</span></div>
      </li>
    </ul>
  </footer>
</template>

<script>
export default {
  name: 'PageFooter',
}
</script>

<style lang="scss" scoped>
@import "~styles/exports";

.footer {
  min-height: 200px;
  padding: 20px 0;
  margin-top: 100px;
  font-size: 12px;
  background-color: #324057;
  color: #768193;

  &__list {
    list-style: none;
    padding-left: 0;
  }

  &__item {
    @include border-box();

    float: left;
    width: 25%;
    padding: 24px 2%;
  }

  &__title {
    font-size: 14px;
    margin: 0 auto 8px;
    font-weight: 500;
    position: relative;
    color: #a4aebd;
  }

  &__body {
    margin: 0 0 5px;

    a {
      color: #eee;
    }
  }
}
</style>
