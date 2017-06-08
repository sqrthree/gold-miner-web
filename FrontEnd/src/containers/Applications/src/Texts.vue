<template>
  <div class="container texts">
    <div class="el-tabs">
      <div class="el-tabs__header">
        <div class="el-tabs__nav-wrap">
          <div class="el-tabs__nav">
            <div class="el-tabs__active-bar" style="width: 88px; transform: translateX(88px);"></div>
            <router-link class="el-tabs__item" :to="'/applications/applicants'">译者申请</router-link>
            <router-link class="el-tabs__item is-active" :to="'/applications/texts'">试译文本</router-link>
          </div>
          <el-button class="pull-right" type="primary" @click="addText">添加文本</el-button>
        </div>
      </div>
    </div>
    <div class="texts__list">
      <el-table :data="textTable" border>
        <el-table-column prop="title" label="标题"></el-table-column>
        <el-table-column prop="categoryName" label="分类"></el-table-column>
        <el-table-column prop="cdate" label="创建日期"></el-table-column>
        <el-table-column label="操作">
          <template scope="scope">
            <el-button type="text" size="small" @click="editText(scope.row.id)">编辑</el-button>
            <el-button type="text" size="small" @click="deleteText(scope.row.id)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
    </div>
    <el-dialog :title="dialog.title" :visible.sync="dialog.isVisible" @close="closeDialog">
      <el-form :model="form" label-position="top" label-width="80px">
        <el-form-item label="标题">
          <el-input v-model="form.title" auto-complete="off"></el-input>
        </el-form-item>
        <el-form-item label="分类">
          <el-select v-model="form.category" placeholder="请选择分类">
            <el-option v-for="item in categories.id"  :key="item" :label="categories.data[item].category" :value="item"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="英文文本">
          <el-input type="textarea" v-model="form.content" auto-complete="off" :rows="6"></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="closeDialog">取 消</el-button>
        <el-button type="primary" @click="saveChange" :loading="loading">确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { mapState, mapGetters } from 'vuex'

export default {
  name: 'ApplicationTexts',
  data() {
    return {
      dialog: {
        title: '',
        isVisible: false,
      },
      form: {
        id: '',
        title: '',
        category: '',
        content: '',
      },
      loading: false,
    }
  },
  computed: {
    ...mapState(['categories']),
    ...mapGetters(['currentUser', 'texts']),
    textTable() {
      return this.texts.map((item) => {
        const category = this.categories.data[item.category]
        item.categoryName = category ? category.category : ''

        return item
      })
    },
  },
  methods: {
    addText() {
      this.dialog = { isVisible: true, title: '添加文本' }
    },
    editText(id) {
      this.dialog = { isVisible: true, title: '编辑文本' }
      const text = this.$store.state.applications.texts.data[id]

      this.form = {
        id: text.id,
        title: text.title,
        category: text.category,
        content: text.content,
      }
    },
    closeDialog() {
      this.dialog.isVisible = false
      this.form = {
        id: '',
        title: '',
        category: '',
        content: '',
      }
    },
    saveChange() {
      let action = ''

      if (this.form.id) {
        action = 'updateText'
      } else {
        action = 'addText'
      }

      const data = Object.assign(this.form, { operator: this.currentUser.id })
      this.loading = true

      this.$store.dispatch(action, data).then(() => {
        this.loading = false
        this.closeDialog()
      }).catch((err) => {
        this.loading = false
        this.$message.error(err.message)
      })
    },
    deleteText(id) {
      this.$store.dispatch('deleteText', id).catch((err) => {
        this.$message.error(err.message)
      })
    },
  },
  created() {
    this.$store.dispatch('fetchTexts')
    this.$store.dispatch('fetchCategories')
  },
}
</script>

<style lang="scss" scoped>
.texts {
  margin-top: 10px;
}
</style>
