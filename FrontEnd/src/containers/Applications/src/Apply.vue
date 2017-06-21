<template>
  <div class="container apply__container" v-loading="loading.status" :element-loading-text="loading.text">
    <el-steps :space="300" :align-center="true" :center="true" :active="active" :process-status="status" finish-status="success">
      <el-step title="填写资料"></el-step>
      <el-step title="进行试译"></el-step>
      <el-step title="提交申请"></el-step>
    </el-steps>
    <el-row class="apply__info" v-if="active === 0">
      <el-col :span="11" :offset="6">
        <el-form ref="form" :model="userInfo" label-width="100px">
          <el-form-item label="邮箱" required>
            <el-input v-model="userInfo.email" placeholder="用于接收试译结果"></el-input>
          </el-form-item>
          <el-form-item label="擅长领域" required>
            <el-select v-model="userInfo.major" placeholder="擅长领域" required>
              <el-option v-for="item in categories.id" :key="item" :label="categories.data[item].category" :value="item"></el-option>
            </el-select>
          </el-form-item>
          <el-form-item label="英语能力简述">
            <el-input type="textarea" v-model="userInfo.description"></el-input>
          </el-form-item>
          <el-form-item>
            <el-button type="primary" @click="submitInfo">下一步</el-button>
          </el-form-item>
        </el-form>
      </el-col>
    </el-row>
    <el-row class="translation" v-if="active === 1">
      <el-col :span="18" :offset="3">
        <h4 class="translation__title">请对下列一段英文进行翻译：</h4>
        <p class="translation__text">{{ article.content }}</p>
        <h4>译文</h4>
        <el-form>
          <el-form-item>
            <el-input type="textarea" v-model="translation" :rows="8"></el-input>
          </el-form-item>
          <el-form-item>
            <el-button class="pull-right" type="primary" @click="submitRequest">下一步</el-button>
          </el-form-item>
        </el-form>
      </el-col>
    </el-row>
    <el-row class="result" v-if="active === 2">
      <el-col :span="8" :offset="8">
        <el-alert v-if="result.type ==='success'" :title="result.message" type="success" show-icon :closable="false"></el-alert>
        <el-alert v-if="result.type ==='failure'" :title="result.message" type="error" show-icon :closable="false"></el-alert>
      </el-col>
    </el-row>
  </div>
</template>

<script>
import { mapState, mapGetters } from 'vuex'

const store = require('store')

export default {
  name: 'ApplyUs',
  data() {
    return {
      active: 0,
      status: 'finish',
      article: {},
      userInfo: {
        email: '',
        major: '',
        description: '',
      },
      translation: '',
      result: {
        type: '',
        message: '',
      },
    }
  },
  computed: {
    ...mapState(['users', 'loading', 'categories']),
    ...mapGetters(['currentUser']),
  },
  methods: {
    // 提交第一步中的基本信息
    submitInfo() {
      const { email, major } = this.userInfo

      if (!email || !major) return

      // 下一步
      this.active = 1

      // 获取试译的英文稿
      this.$store.dispatch('fetchRandomText', major).then((data) => {
        this.article = data
      })
    },
    // 提交翻译的译文和最终数据
    submitRequest() {
      if (!this.translation) return

      const { email, major, description } = this.userInfo
      const { name } = this.currentUser

      // 提交申请信息和翻译数据
      this.$store.dispatch('submitApplication', {
        name,
        email,
        major,
        description,
        articleId: this.article.id,
        translation: this.translation,
      }).then(() => {
        this.active = 2
        this.status = 'success'
        this.result = {
          type: 'success',
          message: `你的请求已成功提交，我们稍后会将结果发送至您的邮箱 ${email}，请注意查收。`,
        }
      }).catch((err) => {
        this.active = 2
        this.status = 'error'
        this.result = {
          type: 'failure',
          message: err.message,
        }
      })
    },
  },
  created() {
    let invitationCode = this.$route.query['invitation-code']

    // 用户只有点击邀请链接才会以附带邀请码的形式访问该地址，此时应将邀请码
    // 存储至 localstorage 然后跳转至登录页面
    if (invitationCode) {
      store.set('invitationCode', invitationCode)
      window.location.href = '/api/auth/login'
      return
    }

    // 待登录后，如果不是译者，会跳转至该页面，然后尝试从
    // 本地存储中读取验证码并发起请求进行校验。
    invitationCode = store.get('invitationCode')

    if (invitationCode) {
      // 本地存在邀请码，则验证该邀请码是否有效，并从本地删除该邀请码。
      store.remove('invitationCode')
      this.$store.dispatch('validateInvitationCode', invitationCode).then((response) => {
        if (response.isValid) {
          this.$message.success('恭喜你成为了我们的新译者。')
          this.$router.replace('/')
        } else {
          this.$message.error('对不起，该邀请码无效。')
        }
      }).catch((err) => {
        this.$message.error(err.message)
      })

      return
    }

    const { email } = this.currentUser

    if (email) {
      this.userInfo.email = email
    }

    this.$store.dispatch('fetchCategories')
  },
}
</script>

<style lang="scss" scoped>
@import "~styles/exports";

.apply {
  &__container {
    margin-top: 40px;
  }

  &__info {
    margin-top: 50px;
  }
}

.translation {
  &__title {
    // font-size: 24px;
  }

  &__text {
    padding: 14px;
    border: 1px solid $gray;
    border-radius: 4px;
    line-height: 24px;
    font-size: 14px;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
  }
}

.result {
  margin-top: 60px;
  margin-bottom: 82px;
}
</style>
