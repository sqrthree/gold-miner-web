<template>
  <div class="popover">
    <el-tabs v-model="active">
      <el-tab-pane label="系统消息" name="system">
        <message :messages="notifications.system"></message>
      </el-tab-pane>
      <el-tab-pane v-if="user.isadmin" label="译者申请" name="applicants">
        <message :messages="notifications.applicants"></message>
      </el-tab-pane>
      <el-tab-pane v-if="user.isadmin" label="推荐的文章" name="articles">
        <message :messages="notifications.recommends"></message>
      </el-tab-pane>
    </el-tabs>
  </div>
</template>

<script>
import Message from './Message'

export default {
  name: 'Popover',
  props: ['notifications', 'user'],
  components: {
    message: Message,
  },
  data() {
    return {
      active: 'system',
    }
  },
}
</script>
