<template>
  <div class="ranking">
    <div class="ranking__hidden-columns"><slot></slot></div>
    <table class="ranking__table">
      <thead class="ranking__head">
        <tr>
          <th class="ranking__item-th" v-for="config in columnConfig">{{ config.label }}</th>
        </tr>
      </thead>
      <tbody class="ranking__body">
        <tr class="ranking__empty" v-if="!data || !data.length"><td colspan="3">暂无数据</td></tr>
        <tr class="ranking__item" v-else v-for="item, index in data">
          <td :class="index < 3 ? 'ranking__item-td ranking-highlight' : 'ranking__item-td'" v-for="config in columnConfig">{{ config.prop === 'index' ? item[config.prop] || index + 1 : item[config.prop] }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  name: 'RankingList',
  props: ['data'],
  data() {
    return {
      columnConfig: [],
    }
  },
}
</script>

<style lang="scss" scoped>
@import "~styles/exports";

.ranking {
  &__hidden-columns {
    display: none;
  }

  &__table {
    width: 100%;
    margin-top: 10px;
    table-layout: fixed;
    border-collapse: collapse;
  }

  &__item-th,
  &__item-td {
    padding: 0 10px;
    font-size: 14px;
    line-height: 32px;
    border-bottom: 1px solid $gray;
    color: $black;

    &:last-child {
      text-align: right;
    }
  }

  &__item-td.ranking-highlight {
    color: $success;
  }

  &__item-th {
    font-weight: 400;
    text-align: left;
  }

  tr:last-child {
    .ranking__item-td {
      border-bottom: none;
    }
  }

  &__empty {
    height: 330px;
    text-align: center;
    color: $silver;
  }
}
</style>
