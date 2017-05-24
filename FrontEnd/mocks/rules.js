const express = require('express')
const chalk = require('chalk')
const mock = require('mockjs').mock

const router = express.Router()

const id = function id() {
  return Math.random().toString().substring(2)
}

const sleep = function sleep(second) {
  return new Promise((resolve) => {
    setTimeout(resolve, second * 1000)
  })
}

router.all('*', (req, res, next) => {
  console.log()
  console.log(`${chalk.green(req.method)} ${chalk.gray(req.url)}`)
  if (Object.keys(req.query).length) {
    console.log(`query:  ${JSON.stringify(req.query)}`)
  }

  if (Object.keys(req.body).length) {
    console.log(`body:   ${JSON.stringify(req.body)}`)
  }

  sleep(2).then(next)
})

router.get('/auth/login', (req, res) => {
  res.redirect('/#/applications')
})

router.post('/auth/validate-invitation-code', (req, res) => {
  const random = Math.round(Math.random())

  if (random) {
    res.json({ isValid: true })
  } else {
    res.status(401).json({ message: '邀请码无效。' })
  }
})

router.get('/auth/logout', (req, res) => {
  res.end()
})

router
  .get('/applications/texts', (req, res) => {
    const data = mock({
      'texts|10': [{
        id: 1,
        type: 'frontend',
        title: '@title',
        text: '@paragraph',
        creatorId: 1,
        cdate: 1494422649139,
        udate: 1494422649139,
      }],
    })

    data.texts.forEach((item, index) => {
      item.id = index + 1
      return item
    })

    res.json(data.texts)
  })
  .post('/applications/texts', (req, res) => {
    res.json(Object.assign({}, req.body, {
      id: 100,
      creatorId: 1,
      cdate: 1494422649139,
      udate: 1494422649139,
    }))
  })
  .put('/applications/texts/:id', (req, res) => {
    res.json(Object.assign({}, req.body, {
      creatorId: 1,
      cdate: 1494422649139,
      udate: 1494422649139,
    }))
  })
  .delete('/applications/texts/:id', (req, res) => {
    res.json({ message: '删除成功' })
  })

router
  .get('/applications/applicants', (req, res) => {
    const data = mock({
      'applicants|10': [{
        'id|+1': 1,
        major: 'frontend',
        ability: '过了 4 级',
        texts: 1,
        translation: '@cparagraph',
        cdate: '@date',
      }]
    })

    return res.json(data.applicants)
  })
  .post('/applications/applicants', (req, res) => {
    return res.json({
      email: req.body.email,
    })
  })

router.get('/articles', (req, res) => {
  const data = mock({
    'articles|10': [{
      'id|+1': 1,
      title: '@ctitle',
      description: '@cparagraph',
      'category|1': ['前端', '后端', 'Android', 'iOS', '设计', '产品', '其他'],
      author: {
        'id|+1': 1,
        username: '@cname',
        avatar: '/static/avatar.png',
      },
      meta: {
        createdAt: '28 分钟前',
      },
    }],
  })

  res.json(data.articles)
})

router.all('*', (req, res) => {
  res.status(404).json({ message: '404 Not found.' })
})

module.exports = router
