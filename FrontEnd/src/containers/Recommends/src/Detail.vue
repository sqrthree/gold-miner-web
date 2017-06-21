<template>
  <div class="container referrals">
    <card title="推荐文章审核">
      <section class="section">
        <h4 class="section__title">文章信息</h4>
        <ul class="information clearfix">
          <li class="information__item"><span class="information__label">推荐分类：</span><strong class="information__value">{{ categories.data[recommend.category] && categories.data[recommend.category].category }}</strong></li>
          <li class="information__item"><span class="information__label">文章链接：</span><strong class="information__value"><a :href="recommend.url">{{ recommend.title }}</a></strong></li>
          <li class="information__item"><span class="information__label">推荐时间：</span><strong class="information__value">{{ recommend.cdate }}</strong></li>
          <li class="information__item"><span class="information__label">推荐理由：</span><strong class="information__value">{{ recommend.description }}</strong></li>
        </ul>
      </section>
      <hr>
      <section class="section">
        <h4 class="section__title">审核意见</h4>
        <div class="opinion-box">
          <el-input class="opinion" type="textarea" v-model="opinion" :rows="4"></el-input>
          <el-button @click="submitOpinion(true)">通过</el-button>
          <el-button @click="submitOpinion(false)">拒绝</el-button>
        </div>
        </el-form>
      </section>
    </card>
  </div>
</template>

<script>
import { mapState } from 'vuex'

export default {
  name: 'ArticleReferrals',
  data() {
    return {
      opinion: '',
    }
  },
  computed: {
    ...mapState(['recommends', 'categories']),
    recommend() {
      const { id } = this.$route.params

      return this.recommends.data[id] || {}
    },
  },
  methods: {
    submitOpinion(result) {
      if (!result && !this.opinion) return this.$message({ message: '留一下意见了啦~', type: 'warning' })

      return this.$store.dispatch('submitOpinionOfRecommends', {
        id: this.recommend.id,
        result,
        opinion: this.opinion,
      }).then(() => {
        this.$message({ message: '提交成功', type: 'success' })
      }).catch((err) => {
        this.$message({ message: err.message, type: 'error' })
      })
    },
  },
  created() {
    this.$store.dispatch('fetchRecommends')
  },
}
</script>

<style lang="scss" scoped>
@import "~styles/exports";

.referrals {
  margin-top: 30px;
}
.information {
  padding-left: 0;
  margin: 0;
  list-style: none;

  &__item {
    float: left;
    width: 33.33%;
    margin-bottom: 12px;
    font-size: 14px;
    text-indent: 10px;

    a {
      color: $primary;
    }
  }

  &__value {
    font-weight: 400;
  }
}

hr {
  border: 0;
  border-bottom:1px solid $gray-light;
}

.section {
  margin-bottom: 30px;

  .text {
    padding: 14px;
    border: 1px solid $gray;
    border-radius: 4px;
    line-height: 24px;
    font-size: 14px;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
  }
}

.opinions {
  margin-bottom: 30px;
}

.opinion {
  margin-bottom: 20px;
}
</style>
