<template>
  <div class="popover">
    <el-tabs v-model="active">
      <el-tab-pane label="系统消息" name="system">
        <message :messages="notifications.system"></message>
      </el-tab-pane>
      <el-tab-pane v-if="user.admin" label="译者申请" name="applicants">
        <message :data="applicants" type="applicants"></message>
      </el-tab-pane>
      <el-tab-pane v-if="user.admin" label="推荐的文章" name="articles">
        <message :data="recommends" type="recommends"></message>
      </el-tab-pane>
    </el-tabs>
  </div>
</template>

<script>
import { mapState, mapGetters } from 'vuex'

import Message from './Message'

export default {
  name: 'Popover',
  props: ['user'],
  components: {
    message: Message,
  },
  computed: {
    ...mapState(['notifications']),
    ...mapGetters(['applicants', 'recommends']),
  },
  data() {
    return {
      active: 'system',
    }
  },
}
</script>
