<template>
  <div class="container">
    <el-row :gutter="20">
      <el-col :span="8">
        <ranking-list :data="rankingList.translations"></ranking-list>
      </el-col>
      <el-col :span="8">
        <ranking-list :data="rankingList.translations"></ranking-list>
      </el-col>
      <el-col :span="8">
        <ranking-list :data="rankingList.translations"></ranking-list>
      </el-col>
    </el-row>
  </div>
</template>

<script>
const data = {
  title: '翻译的文章',
  list: [
    {
      id: 1,
      title: '如何为超级复杂如此复杂真的很复杂的真的很复杂的',
    },
    {
      id: 2,
      title: '我是译者本人，这是我翻译的文章',
    },
    {
      id: 3,
      title: '如何为复杂应用设计表单',
    },
    {
      id: 4,
      title: '杂如此复杂真的很',
    },
    {
      id: 5,
      title: '如何为超级复杂如此复的真的很复杂的',
    },
  ],
}

export default {
  name: 'UserArticles',
  data() {
    return {
      rankingList: {
        translations: data,
      },
    }
  },
}
</script>

<style lang="scss" scoped>
@import "~styles/exports";

</style>
