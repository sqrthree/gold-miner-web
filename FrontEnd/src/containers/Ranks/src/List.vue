<template>
  <div class="rank container">
    <div class="rank__list">
      <el-row :gutter="20">
        <el-col :span="8">
          <card title="推荐月榜" size="small">
            <ranking-list :data="ranking.recommend.month">
              <ranking-column prop="index" label="排名"></ranking-column>
              <ranking-column prop="name" label="译者"></ranking-column>
              <ranking-column prop="num" label="翻译文章"></ranking-column>
            </ranking-list>
          </card>
        </el-col>
        <el-col :span="8">
          <card title="推荐年榜" size="small">
            <ranking-list :data="ranking.recommend.year">
              <ranking-column prop="index" label="排名"></ranking-column>
              <ranking-column prop="name" label="译者"></ranking-column>
              <ranking-column prop="num" label="翻译文章"></ranking-column>
            </ranking-list>
          </card>
        </el-col>
        <el-col :span="8">
          <card title="推荐总榜" size="small">
            <ranking-list :data="ranking.recommend.total">
              <ranking-column prop="index" label="排名"></ranking-column>
              <ranking-column prop="name" label="译者"></ranking-column>
              <ranking-column prop="num" label="翻译文章"></ranking-column>
            </ranking-list>
          </card>
        </el-col>
      </el-row>
    </div>
    <div class="rank__list">
      <el-row :gutter="20">
        <el-col :span="8">
          <card title="翻译月榜" size="small">
            <ranking-list :data="ranking.translate.month">
              <ranking-column prop="index" label="排名"></ranking-column>
              <ranking-column prop="name" label="译者"></ranking-column>
              <ranking-column prop="num" label="翻译文章"></ranking-column>
            </ranking-list>
          </card>
        </el-col>
        <el-col :span="8">
          <card title="翻译年榜" size="small">
            <ranking-list :data="ranking.translate.year">
              <ranking-column prop="index" label="排名"></ranking-column>
              <ranking-column prop="name" label="译者"></ranking-column>
              <ranking-column prop="num" label="翻译文章"></ranking-column>
            </ranking-list>
          </card>
        </el-col>
        <el-col :span="8">
          <card title="翻译总榜" size="small">
            <ranking-list :data="ranking.translate.total">
              <ranking-column prop="index" label="排名"></ranking-column>
              <ranking-column prop="name" label="译者"></ranking-column>
              <ranking-column prop="num" label="翻译文章"></ranking-column>
            </ranking-list>
          </card>
        </el-col>
      </el-row>
    </div>
    <div class="rank__list">
      <el-row :gutter="20">
        <el-col :span="8">
          <card title="校对月榜" size="small">
            <ranking-list :data="ranking.review.month">
              <ranking-column prop="index" label="排名"></ranking-column>
              <ranking-column prop="name" label="译者"></ranking-column>
              <ranking-column prop="num" label="翻译文章"></ranking-column>
            </ranking-list>
          </card>
        </el-col>
        <el-col :span="8">
          <card title="校对年榜" size="small">
            <ranking-list :data="ranking.review.year">
              <ranking-column prop="index" label="排名"></ranking-column>
              <ranking-column prop="name" label="译者"></ranking-column>
              <ranking-column prop="num" label="翻译文章"></ranking-column>
            </ranking-list>
          </card>
        </el-col>
        <el-col :span="8">
          <card title="校对总榜" size="small">
            <ranking-list :data="ranking.review.total">
              <ranking-column prop="index" label="排名"></ranking-column>
              <ranking-column prop="name" label="译者"></ranking-column>
              <ranking-column prop="num" label="翻译文章"></ranking-column>
            </ranking-list>
          </card>
        </el-col>
      </el-row>
    </div>
  </div>
</template>

<script>
import assign from 'lodash/assign'
import * as statisticService from '@/services/statistics'

export default {
  name: 'Rank',
  data() {
    return {
      ranking: {
        recommend: {
          week: [],
          month: [],
          year: [],
        },
        translate: {
          week: [],
          month: [],
          year: [],
        },
        review: {
          week: [],
          month: [],
          year: [],
        },
      },
    }
  },
  created() {
    statisticService.fetchRanks().then((data) => {
      assign(this.ranking, data)
    }).catch(err => this.$message.error(err.message))
  },
}
</script>

<style lang="scss" scoped>
.rank {
  margin-top: 40px;

  &__list {
    margin-bottom: 30px;
  }
}
</style>
