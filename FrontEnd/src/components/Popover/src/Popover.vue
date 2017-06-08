<template>
  <div class="popover">
    <el-tabs v-model="active">
      <el-tab-pane v-if="notifications.applicants" label="译者申请" name="applicants">
        <message :messages="notifications.applicants"></message>
      </el-tab-pane>
      <el-tab-pane v-if="notifications.recommends" label="推荐的文章" name="articles">
        <message :messages="notifications.recommends"></message>
      </el-tab-pane>
    </el-tabs>
  </div>
</template>

<script>
import Message from './Message'

export default {
  name: 'Popover',
  props: ['notifications'],
  components: {
    message: Message,
  },
  data() {
    return {
      active: 'applicants',
    }
  },
}
</script>
