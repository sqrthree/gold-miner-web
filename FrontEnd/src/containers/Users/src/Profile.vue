<template>
  <div class="profile">
    <el-row>
      <el-col :span="14">
        <div class="detail">
          <div class="detail__header">
            <div class="detail__divider"></div>
            <a class="detail__extra" v-if="!isMyself" :href="`mailto:${user.email}`">和大神聊聊</a>
            <strong class="detail__title">译者档案馆</strong>
          </div>
          <div class="detail__body">
            <el-row>
              <el-col :span="12">
                <ul class="document">
                  <li class="document__item">
                    <strong class="document__label">加入日期</strong>
                    <span class="document__text">{{ user.cdate }}</span>
                  </li>
                </ul>
              </el-col>
              <el-col :span="12">
                <ul class="document">
                  <li class="document__item">
                    <strong class="document__label">翻译文章</strong>
                    <span class="document__text"><router-link :to="`/users/${user.id}/articles`">{{ user.translateNumber }}</router-link> 篇</span>
                  </li>
                  <li class="document__item">
                    <strong class="document__label">参与校对</strong>
                    <span class="document__text"><router-link :to="`/users/${user.id}/articles`">{{ user.reviewNumber }}</router-link> 篇</span>
                  </li>
                  <li class="document__item">
                    <strong class="document__label">推荐文章</strong>
                    <span class="document__text"><router-link :to="`/users/${user.id}/articles`">{{ user.recommendNumber }}</router-link> 篇</span>
                  </li>
                  <li class="document__item">
                    <strong class="document__label">总获积分</strong>
                    <span class="document__text">{{ user.totalScore }}</span>
                  </li>
                  <li class="document__item">
                    <strong class="document__label">当前积分</strong>
                    <span class="document__text">{{ user.currentScore }}</span>
                  </li>
                </ul>
              </el-col>
            </el-row>
          </div>
        </div>
      </el-col>
      <el-col :span="10">
        <div class="detail ranking-list">
          <div class="detail__header">
            <div class="detail__divider"></div>
            <a class="detail__extra" href="javascript:;" v-if="isMyself" @click="showSettingsDialog()">我的设置</a>
            <strong class="detail__title">成就排行</strong>
          </div>
          <div class="detail__body">
            <ul class="ranking clearfix">
              <li class="ranking__item"><strong class="ranking__value">{{ this.rank.recommend }}</strong><p class="ranking__label">推荐排名</p></li>
              <li class="ranking__item"><strong class="ranking__value">{{ this.rank.translate }}</strong><p class="ranking__label">翻译排名</p></li>
              <li class="ranking__item"><strong class="ranking__value">{{ this.rank.score }}</strong><p class="ranking__label">积分排名</p></li>
            </ul>
          </div>
        </div>
      </el-col>
    </el-row>
    <el-dialog title="我的设置" :visible.sync="settings.visible" @close="closeSettingsDialog()">
      <el-row v-loading.body="settings.loading">
        <el-col :span="12" :offset="6">
          <el-form :model="settings.values" label-width="200px" label-position="left">
            <el-form-item label="当有新的翻译任务时通知我">
              <el-switch v-model="settings.values.newtranslation" on-text="" off-text="" :on-value="1" :off-value="0"></el-switch>
            </el-form-item>
            <el-form-item label="当有新的校对任务时通知我">
              <el-switch v-model="settings.values.newreview" on-text="" off-text="" :on-value="1" :off-value="0"></el-switch>
            </el-form-item>
            <el-form-item label="有新译文翻译好时通知我">
              <el-switch v-model="settings.values.newarticle" on-text="" off-text="" :on-value="1" :off-value="0"></el-switch>
            </el-form-item>
            <el-form-item label="推荐的文章有结果时通知我">
              <el-switch v-model="settings.values.newresult" on-text="" off-text="" :on-value="1" :off-value="0"></el-switch>
            </el-form-item>
          </el-form>
        </el-col>
      </el-row>
      <div slot="footer" class="dialog-footer">
        <el-button @click="closeSettingsDialog()">取 消</el-button>
        <el-button type="primary" @click="saveSettings()">确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { mapState, mapGetters } from 'vuex'
import assign from 'lodash/assign'
import * as userService from '@/services/users'
import * as statisticService from '@/services/statistics'

export default {
  name: 'Profile',
  beforeRouteEnter(to, from, next) {
    if (to.hash === '#settings') {
      next(vm => vm.showSettingsDialog())
    } else {
      next()
    }
  },
  beforeRouteUpdate(to, from, next) {
    if (to.hash === '#settings') {
      this.showSettingsDialog()
    }

    next()
  },
  data() {
    return {
      settings: {
        loading: false,
        visible: false,
        values: {
          newtranslation: 1,
          newreview: 1,
          newarticle: 1,
          newresult: 1,
        },
      },
      rank: {
        translate: 0,
        recommend: 0,
        score: 0,
      },
    }
  },
  computed: {
    ...mapState(['users']),
    ...mapGetters(['currentUser']),
    user() {
      const { id } = this.$route.params
      return this.users.data[id] || {}
    },
    isMyself() {
      if (this.user.id && this.user.id === this.currentUser.id) {
        return true
      }

      return false
    },
  },
  methods: {
    showSettingsDialog() {
      if (!this.isMyself) {
        return
      }

      this.settings.visible = true
      this.settings.loading = true

      userService.fetchSettings().then((data) => {
        assign(this.settings.values, data)
        this.settings.loading = false
      }).catch((err) => {
        this.$message.error(err.message)
        this.settings.loading = false
      })
    },
    closeSettingsDialog() {
      this.settings.loading = false
      this.settings.visible = false
      window.location.hash = ''
    },
    saveSettings() {
      this.settings.loading = true

      userService.updateSettings({
        newtranslation: this.settings.values.newtranslation,
        newreview: this.settings.values.newreview,
        newarticle: this.settings.values.newarticle,
        newresult: this.settings.values.newresult,
      }).then(() => {
        this.$message.success('保存成功。')
        this.closeSettingsDialog()
      }).catch((err) => {
        this.$message.error(err.message)
        this.settings.loading = false
      })
    },
  },
  created() {
    const { id } = this.$route.params

    statisticService.fetchRankOfUser(id).then((data) => {
      assign(this.rank, data)
    }).catch((err) => {
      this.$message.error(err.message)
    })
  },
}
</script>

<style lang="scss" scoped>
@import "~styles/exports";

.profile {
  margin-bottom: 100px;
}

.detail {
  padding: 0 20px;

  &__header {
    position: relative;
    height: 30px;
    line-height: 30px;
  }

  &__title {
    padding-right: 10px;
    font-size: 16px;
    background: #fff;
  }

  &__extra {
    float: right;
    padding-left: 10px;
    font-size: 12px;
    color: $blue;
    background: #fff;
  }

  &__divider {
    position: absolute;
    top: 15px;
    width: 100%;
    height: 1px;
    background: $gray-light;
    z-index: -1;
  }
}

.document {
  list-style: none;
  padding-left: 0;

  &__item {
    height: 30px;
    line-height: 30px;
    padding-left: 40px;
    font-size: 14px;

    a {
      color: $blue;
    }
  }

  &__label {
    font-weight: 400;
    margin-right: 24px;
  }
}

.ranking {
  padding: 20px;
  list-style: none;

  &__item {
    float: left;
    width: 33.33%;
    text-align: center;
  }

  &__value {
    font-size: 24px;
    font-weight: 400;
  }

  &__label {
    font-size: 14px;
  }
}
</style>
