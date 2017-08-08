<template>
  <div class="article__box">
    <div class="article__metacle clearfix">
      <div class="article__tags pull-left">
        <span class="article__tag">iOS</span>
        <span class="article__tag">2017-07-10</span>
      </div>
      <div class="article__links pull-right">
        <a v-if="article.link" :href="article.link" class="article__link">去掘金收藏该文章</a>
        <a v-if="showAddLinkBtn" href="javascript:;" class="article__link" @click="showDialog()">添加掘金译文链接</a>
      </div>
    </div>
    <div class="article__content">
      <h1>文章正文内容</h1>
    </div>
    <el-dialog title="编辑文章" :visible.sync="dialog.isVisible" @close="closeDialog()">
      <el-form :model="dialog.data" label-width="140px">
        <el-form-item label="掘金译文链接">
          <el-input v-model="dialog.data.link"></el-input>
        </el-form-item>
        <el-form-item label="文章简介">
          <el-input type="textarea" v-model="dialog.data.description"></el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="closeDialog()">取 消</el-button>
        <el-button type="primary" @click="saveChange()" :loading="dialog.loading">确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import pick from 'lodash/pick'
import assign from 'lodash/assign'
import * as articleServices from '@/services/articles'

export default {
  name: 'ArticleItem',
  data() {
    return {
      article: {},
      loading: false,
      dialog: {
        isVisible: false,
        loading: false,
        data: {
          link: '',
          description: '',
        },
      },
    }
  },
  computed: {
    ...mapGetters(['currentUser']),
    showAddLinkBtn() {
      if (this.currentUser.admin) {
        return true
      }

      if (this.article.translator && this.article.translator.id === this.currentUser.id) {
        return true
      }

      return false
    },
  },
  methods: {
    showDialog() {
      this.dialog.isVisible = true

      assign(this.dialog.data, pick(this.article, [
        'link',
        'description',
      ]))
    },

    closeDialog() {
      this.dialog.isVisible = false
    },

    saveChange() {
      this.dialog.loading = true

      articleServices.updateArticleWithId(this.article.id, this.dialog.data).then(() => {
        this.dialog.loading = false
        this.closeDialog()
        assign(this.article, this.dialog.data)
        return this.$message.success('更新成功')
      }).catch((err) => {
        this.dialog.loading = false
        this.closeDialog()
        return this.$message.error(err.message)
      })
    },
  },
  created() {
    const { id } = this.$route.params
    const article = this.$store.state.articles.data[id]

    if (article) {
      this.article = article
      return
    }

    articleServices.fetchArticleWithId(id).then((data) => {
      this.article = data
    }).catch(err => this.$message.error(err.message))
  },
}
</script>

<style lang="scss" scoped>
@import "~styles/exports";

.article {
  &__box {
    min-height: 400px;
    padding: 30px 40px;
    border: 1px solid $gray;
    border-radius: 6px;
  }

  &__meta {
    font-size: 16px;
  }

  &__tag {
    margin-right: 20px;
    color: $silver;

    &:before {
      margin-right: 4px;
      content: '·';
      font-weight: 900;
      color: $primary;
    }
  }

  &__link {
    color: $primary;

    &:after {
      margin: 0 4px;
      content: '|';
      vertical-align: text-top;
      color: $silver-extra-light;
    }

    &:last-child:after {
      display: none;
    }
  }
}
</style>
