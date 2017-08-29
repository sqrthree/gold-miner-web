<template>
  <div class="container applications">
    <el-breadcrumb class="breadcrumb">
      <el-breadcrumb-item :to="{ path: '/' }">首页</el-breadcrumb-item>
      <el-breadcrumb-item :to="{ path: '/applications/applicants' }">申请加入的用户列表</el-breadcrumb-item>
      <el-breadcrumb-item :to="{ path: `/applications/applicants/${applicantId}` }">新译者</el-breadcrumb-item>
    </el-breadcrumb>
    <card title="申请成为译者">
      <section class="section">
        <h4 class="section__title">申请人信息</h4>
        <ul class="information clearfix">
          <li class="information__item"><span class="information__label">申请时间：</span><strong class="information__value">{{ applicantInfo.cdate }}</strong></li>
          <li class="information__item"><span class="information__label">擅长的领域：</span><strong class="information__value">{{ applicantInfo.major }}</strong></li>
          <li class="information__item"><span class="information__label">英语能力/翻译经验：</span><strong class="information__value">{{ applicantInfo.description }}</strong></li>
        </ul>
      </section>
      <hr>
      <section class="section">
        <h4 class="section__title">试译文本</h4>
        <p class="text">{{ applications.texts.data[applicantInfo.articleId] ? applications.texts.data[applicantInfo.articleId].content : '没有匹配到数据' }}</p>
      </section>
      <section class="section">
        <h4>译文</h4>
        <p class="text">{{ applicantInfo.translation }}</p>
      </section>
      <section class="section" v-loading="loading" element-loading-text="拼命请求中">
        <h4 class="section__title">审核意见</h4>
        <el-table class="opinions" v-if="applicantInfo.opinions && applicantInfo.opinions.length" :data="applicantInfo.opinions" border>
          <el-table-column prop="result" label="审核结果">
            <template scope="scope">
              <el-tag v-if="scope.row.result" type="success">通过</el-tag>
              <el-tag v-else type="danger">拒绝</el-tag>
            </template>
          </el-table-column>
          <el-table-column prop="date" label="审核日期"></el-table-column>
          <el-table-column prop="username" label="审核人"></el-table-column>
          <el-table-column prop="opinion" label="审核意见"></el-table-column>
        </el-table>
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
  name: 'ApplicationUser',
  data() {
    return {
      opinion: '',
      loading: false,
    }
  },
  computed: {
    ...mapState(['applications']),
    applicantId() {
      return Number(this.$route.params.id)
    },
    applicantInfo() {
      return this.applications.applicants.data[this.applicantId] || {}
    },
  },
  methods: {
    submitOpinion(result) {
      if (!result && !this.opinion) return this.$message({ message: '留一下意见了啦~', type: 'warning' })

      this.loading = true

      return this.$store.dispatch('submitOpinionOfApplications', {
        id: this.applicantId,
        result,
        opinion: this.opinion,
      }).then(() => {
        this.$message.success('提交成功')
        this.loading = false
        this.$router.replace('/applications/applicants')
      }).catch((err) => {
        this.$message.error(err.message)
        this.loading = false
      })
    },
  },
  created() {
    if (!this.applications.texts.id.length) {
      this.$store.dispatch('fetchTexts')
    }
  },
}
</script>

<style lang="scss" scoped>
@import "~styles/exports";

.applications {
  margin-top: 6px;
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
