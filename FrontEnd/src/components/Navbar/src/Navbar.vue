<template>
  <div class="navbar">
    <div class="container">
      <div class="navbar__header">
        <a class="navbar__brand" href="/">掘金翻译计划</a>
      </div>
      <div class="navbar__body">
        <el-menu :default-active="activeIndex" mode="horizontal" router>
          <el-menu-item index="index" :route="{ path:'/' }">首页</el-menu-item>
          <!-- <el-menu-item index="topics">优秀专题</el-menu-item>
          <el-menu-item index="articles">文章集</el-menu-item>
          <el-menu-item index="ranks">排行榜</el-menu-item> -->
          <li class="pull-right">
            <template v-if="logIn">
              <el-menu-item v-if="user.istranslator" index="recomment" :route="{ path: '/recommends' }">推荐文章</el-menu-item>
              <el-menu-item v-else index="Apply" :route="{ path: '/applications/apply' }">加入我们</el-menu-item>
              <el-menu-item class="navbar__messages" index="">
                <el-badge class="item" :value="notifications.total" v-popover:popover>
                  <i class="el-icon-message"></i>
                  <el-popover ref="popover" placement="top">
                    <popover :notifications="notifications" :user="user"></popover>
                  </el-popover>
                </el-badge>
              </el-menu-item>
              <el-submenu class="user-submenu" index="user">
                <template slot="title">{{ user.name }}</template>
                <el-menu-item index="user-home" :route="{ path: `/users/${user.name}` }">我的主页</el-menu-item>
                <el-menu-item index="user-settings" :route="{ path: `/users/${user.name}#settings` }">个人设置</el-menu-item>
                <el-menu-item index="logout" :route="{ path: '/auth/logout' }">退出</el-menu-item>
              </el-submenu>
            </template>
            <template v-else>
              <el-menu-item index="apply" :route="{ path: '/applications/apply' }">加入我们</el-menu-item>
              <el-menu-item index=""><a href="/auth/login">使用 GitHub 登录</a></el-menu-item>
            </template>
          </li>
        </el-menu>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapActions } from 'vuex'

export default {
  name: 'Navbar',
  props: ['user'],
  data() {
    return {
      activeIndex: 'index',
    }
  },
  computed: {
    ...mapState(['notifications']),
    logIn() {
      return this.$store.getters.logIn
    },
  },
  methods: {
    ...mapActions(['fetchNotifications']),
  },
  created() {
    if (this.user.istranslator) {
      this.fetchNotifications()
    }
  },
}
</script>

<style lang="scss">
.navbar {
  &__header {
    float: left;
    position: relative;
    z-index: 1;
  }

  &__brand {
    display: block;
    line-height: 60px;
    padding: 0 15px;
    font-size: 18px;
    color: #48576a;
  }

  &__messages {
    .el-badge__content {
      top: 20px;
      right: 20px;
    }
  }
}

.el-menu--horizontal .el-menu-item:hover {
  border-bottom: 5px solid #20a0ff;
}
.el-menu--horizontal .user-submenu .el-menu-item:hover {
  border-bottom: 5px solid #d1dbe5;
}
.el-menu--horizontal .el-menu-item.navbar__messages:hover {
  border-bottom: 5px solid #eef1f6;
}
.el-menu-item a {
  display: block;
}
</style>
