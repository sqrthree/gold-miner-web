<template>
  <div class="profile">
    <el-row>
      <el-col :span="14">
        <div class="detail">
          <div class="detail__header">
            <div class="detail__divider"></div>
            <a class="detail__extra" :href="`mailto:${currentUser.email}`">和大神聊聊</a>
            <strong class="detail__title">译者档案馆</strong>
          </div>
          <div class="detail__body">
            <el-row>
              <el-col :span="12">
                <ul class="document">
                  <li class="document__item">
                    <strong class="document__label">加入日期</strong>
                    <span class="document__text">{{ currentUser.cdate }}</span>
                  </li>
                </ul>
              </el-col>
              <el-col :span="12">
                <ul class="document">
                  <li class="document__item">
                    <strong class="document__label">翻译文章</strong>
                    <span class="document__text"><router-link :to="'/users/sqrthree/articles'">281</router-link> 篇</span>
                  </li>
                  <li class="document__item">
                    <strong class="document__label">参与校对</strong>
                    <span class="document__text"><router-link :to="'/users/sqrthree/articles'">281</router-link> 篇</span>
                  </li>
                  <li class="document__item">
                    <strong class="document__label">推荐文章</strong>
                    <span class="document__text"><router-link :to="'/users/sqrthree/articles'">281</router-link> 篇</span>
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
            <a class="detail__extra" href="javascript:;" @click="showSettingsDialog()">我的设置</a>
            <strong class="detail__title">成就排行</strong>
          </div>
          <div class="detail__body">
            <ul class="ranking clearfix">
              <li class="ranking__item"><strong class="ranking__value">100</strong><p class="ranking__label">推荐排名</p></li>
              <li class="ranking__item"><strong class="ranking__value">100</strong><p class="ranking__label">翻译排名</p></li>
              <li class="ranking__item"><strong class="ranking__value">100</strong><p class="ranking__label">积分排名</p></li>
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
import { mapGetters } from 'vuex'
import assign from 'lodash/assign'
import * as userService from '@/services/users'

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
        },
      },
    }
  },
  computed: {
    ...mapGetters(['currentUser']),
  },
  methods: {
    showSettingsDialog() {
      this.settings.visible = true
      this.settings.loading = true

      userService.fetchSettings(this.currentUser.id).then((data) => {
        assign(this.settings.values, data)
        this.settings.loading = false
      }).catch((err) => {
        this.$message.error(err.message)
        this.settings.loading = false
      })
    },
    closeSettingsDialog() {
      this.settings.visible = false
      window.location.hash = ''
    },
    saveSettings() {
      this.settings.loading = true

      userService.updateSettings(this.currentUser.id, {
        newtranslation: this.settings.values.newtranslation,
        newreview: this.settings.values.newreview,
        newarticle: this.settings.values.newarticle,
      }).then(() => {
        this.settings.visible = false
        this.settings.loading = false
        this.$message.success('保存成功。')
      }).catch((err) => {
        this.$message.error(err.message)
        this.settings.loading = false
      })
    },
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
