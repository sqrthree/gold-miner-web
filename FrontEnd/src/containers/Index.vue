<template>
  <div class="container">
    <el-row class="main grid__row-gutter">
      <el-col class="grid__col-gutter" :span="18">
        <el-tabs v-model="activeTab" v-loading="loading" @tab-click="toggleTab">
          <el-tab-pane v-for="tab in articlesTab" :label="tab.label" :name="tab.name" :key="tab.name">
            <article-item v-for="item in articles[tab.name].list" :article="articles.data[item]" :key="item"></article-item>
            <div class="text-center load-more">
              <a href="javascript:;" @click="nextPage()">查看更多</a>
              <div class="line"></div>
            </div>
          </el-tab-pane>
        </el-tabs>
      </el-col>
      <el-col class="grid__col-gutter" :span="6">
        <div class="card">
          <h3 class="card__title">加入我们</h3>
          <div><img class="img-rounded" src="/static/images/join.jpg" alt=""></div>
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
      loading: false,
    }
  },
  computed: {
    ...mapState(['articles']),
    activeTab() {
      return this.$store.getters.currentUser.translator ? 'waiting' : 'lastest'
    },
  },
  beforeRouteLeave(to, from, next) {
    next()
  },
  methods: {
    ...mapActions(['fetchArticles']),

    toggleTab(tab) {
      this.fetchArticles({
        type: tab.name,
        page: 1,
      })
    },

    nextPage() {
      const { page } = this.articles[this.activeTab]

      this.fetchArticles({
        type: this.activeTab,
        page: page + 1,
      })
    },
  },
  created() {
    this.loading = true

    this.fetchArticles({
      type: this.activeTab,
      page: 1,
    }).then(() => {
      this.loading = false
    })
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
  img {
    width: 100%;
    height: 200px;
  }
}
</style>
