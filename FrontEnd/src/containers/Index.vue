<template>
  <div class="container">
    <el-row class="main grid__row-gutter">
      <el-col class="grid__col-gutter" :span="18">
        <el-tabs v-model="activeTab" v-loading="loading.status">
          <el-tab-pane v-for="tab in articlesTab" :label="tab.label" :name="tab.name" :key="tab.name">
            <article-item v-for="item in articles[tab.name].data" :article="item" :key="item"></article-item>
            <div class="text-center load-more">
              <a href="javascript:;">查看更多</a>
              <div class="line"></div>
            </div>
          </el-tab-pane>
        </el-tabs>
      </el-col>
      <el-col class="grid__col-gutter" :span="6">
        <div class="card">
          <h3 class="card__title">加入我们</h3>
          <div><img class="img-rounded" src="https://cdn.dribbble.com/users/505523/screenshots/2827970/_______.jpg" alt=""></div>
        </div>
      </el-col>
    </el-row>
  </div>
</template>

<script>
import { mapState, mapActions } from 'vuex'

const articlesTab = [
  {
    name: 'lastest',
    label: '最新译文',
  },
  {
    name: 'waiting',
    label: '等待认领',
  },
  {
    name: 'doing',
    label: '正在进行',
  },
]

export default {
  name: 'Index',
  data() {
    return {
      articlesTab,
    }
  },
  computed: {
    ...mapState(['loading', 'articles']),
    activeTab() {
      return this.$store.getters.currentUser.istranslator ? 'waiting' : 'lastest'
    },
  },
  beforeRouteLeave(to, from, next) {
    this.$store.commit('hideLoading')
    next()
  },
  methods: {
    ...mapActions(['fetchArticles']),
  },
  created() {
    this.fetchArticles({ type: this.activeTab })
  },
}
</script>

<style lang="scss" scoped>
@import "~styles/exports";

.main {
  margin-top: $grid-gutter-width * 1.5;
}
.grid__row-gutter {
  margin-left: floor(-$grid-gutter-width / 2);
  margin-right: ceil(-$grid-gutter-width / 2);
}
.grid__col-gutter {
  padding-left: floor($grid-gutter-width / 2);
  padding-right: ceil($grid-gutter-width / 2);
}

.load-more {
  height: 60px;
  margin: 60px 0;

  .line {
    margin-top: -30px;
    height: 1px;
    background-color: $silver-extra-light;
  }

  a {
    display: inline-block;
    height: 60px;
    line-height: 60px;
    padding: 0 20px;
    font-size: 14px;
    background-color: #fff;
    color: $silver-extra-light;
  }
}

.card {
  &__title {
  }

  img {
    width: 100%;
    height: 200px;
  }
}
</style>
