<template>
  <div class="archive">
    <div class="dashboard__box">
      <ul class="dashboard clearfix">
        <li class="dashboard__item dashboard__item--blue">
          <div class="dashboard__body">
            <i class="dashboard__icon el-icon-document"></i>
            <span class="dashboard__title">全站总翻译词汇量</span>
            <strong class="dashboard__number">{{ overview.words }}</strong>
          </div>
        </li>
        <li class="dashboard__item dashboard__item--green">
          <div class="dashboard__body">
            <i class="dashboard__icon el-icon-information"></i>
            <span class="dashboard__title">共产出优质译文</span>
            <strong class="dashboard__number">{{ overview.articles }}</strong>
          </div>
        </li>
        <li class="dashboard__item dashboard__item--gray">
          <div class="dashboard__body">
            <i class="dashboard__icon el-icon-menu"></i>
            <span class="dashboard__title">参与贡献的优秀译者</span>
            <strong class="dashboard__number">{{ overview.translators }}</strong>
          </div>
        </li>
      </ul>
    </div>
    <el-row class="main grid__row-gutter">
      <el-col class="grid__col-gutter" :span="18">
        <ul class="article">
          <!-- <li class="article__item" v-for="i in 3">
            <div class="article__month">2017.01</div>
            <ul class="article__list">
              <li class="article__body">
                <article-item v-for="item in articles" :article="item" :key="item"></article-item>
              </li>
            </ul>
          </li> -->
        </ul>
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
import assign from 'lodash/assign'
import * as statisticService from '@/services/statistics'

export default {
  name: 'ArticleList',
  data() {
    return {
      overview: {
        translators: 0,
        words: 0,
        articles: 0,
      },
      articles: [
        {
          id: 1,
          title: '带快算',
          description: '区叫强界和议花转万七党点安。音立过际度始事质还容知已。文电计相海王志一立地精将展实要。三图候五取音部具受适则门。',
          category: 'iOS',
          author: {
            id: 1,
            username: '根号三',
            avatar: '/static/images/default-avatar.png',
          },
          status: 2,
          meta: {
            createdAt: '28 分钟前',
          },
        },
      ],
    }
  },
  created() {
    statisticService.fetchOverview().then((data) => {
      assign(this.overview, data)
    }).catch(err => this.$message.error(err.message))
  },
}
</script>

<style lang="scss" scoped>
@import "~styles/exports";

.dashboard {
  padding-left: 0;
  margin-left: -30px;
  margin-top: 0;
  margin-bottom: 40px;
  list-style: none;

  &__box {
    overflow: hidden;
  }

  &__item {
    float: left;
    width: 340px;
    height: 120px;
    margin-left: 30px;
    color: #fff;
    border-radius: 6px;

    &--blue {
      background-color: #2db6f4;
    }

    &--green {
      background-color: #7dc856;
    }

    &--gray {
      background-color: #324057;
    }
  }

  &__body {
    margin-left: 50px;
    margin-top: 30px;
  }

  &__icon {
    float: left;
    padding-top: 8px;
    padding-right: 20px;
    font-size: 42px;
  }

  &__title {
    display: block;
    font-size: 14px;
  }

  &__number {
    font-size: 32px;
  }
}

.article {
  padding-right: 30px;

  &,
  &__list {
    padding-left: 0;
    list-style: none;
  }

  &__item {
    margin-bottom: 50px;
  }

  &__month {
    margin-bottom: 20px;
    font-size: 28px;
    color: $black-extra-light;
  }
}

.card {
  img {
    width: 100%;
    height: 200px;
  }
}
</style>
