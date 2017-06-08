import Vue from 'vue'
import Router from 'vue-router'
import nprogress from 'nprogress'

import Index from '@/containers/Index'
import Recommend from '@/containers/Recommend'
import Applications from '@/containers/Applications'
import Auth from '@/containers/Auth'
import Articles from '@/containers/Articles'

Vue.use(Router)

const router = new Router({
  routes: [
    {
      path: '/',
      name: 'Index',
      component: Index,
      meta: {
        title: '掘金翻译计划',
      },
    },
    {
      path: '/recommend',
      name: 'Recommend',
      component: Recommend,
      meta: {
        title: '推荐文章',
      },
    },
    {
      path: '/applications',
      component: Applications.Base,
      meta: {
        title: '加入我们',
      },
      children: [
        {
          path: 'apply',
          name: 'Apply',
          component: Applications.Apply,
          meta: {
            title: '加入我们',
          },
        },
        {
          path: 'applicants',
          name: 'applicants',
          component: Applications.Applicants,
          meta: {
            title: '译者申请列表',
          },
        },
        {
          path: 'applicants/:id',
          name: 'applicant',
          component: Applications.Applicant,
          meta: {
            title: '译者申请',
          },
        },
        {
          path: 'texts',
          name: 'TextsForApplication',
          component: Applications.Texts,
          meta: {
            title: '试译文本列表',
          },
        },
      ],
    },
    {
      path: '/auth',
      name: 'Auth',
      component: Auth.Base,
      children: [
        {
          path: 'logout',
          name: 'Logout',
          component: Auth.Logout,
        },
      ],
    },
    {
      path: '/articles',
      component: Articles.Base,
      children: [
        {
          path: '',
          name: 'ArticleList',
          component: Articles.List,
          meta: {
            title: '文章列表',
          },
        },
        {
          path: ':id',
          name: 'ArticleItem',
          component: Articles.Item,
        },
        {
          path: ':id/details',
          name: 'ArticleDetail',
          component: Articles.Detail,
        },
        {
          path: ':id/referrals',
          name: 'ArticleReferrals',
          component: Articles.Referrals,
        },
      ],
    },
  ],
})

router.afterEach((to) => {
  nprogress.done()
  document.title = to.meta.title || '掘金翻译计划'
})

export default router
