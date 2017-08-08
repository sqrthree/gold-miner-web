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
  res.redirect('/applications')
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

router.get('/notifications', (req, res) => {
  const data = mock({
    system: [],
  })

  return res.json(data.system)
})

router
  .get('/articles/random/:category', (req, res) => {
    res.json(mock({
      id: 1,
      type: 'frontend',
      title: '@title',
      content: '@paragraph',
      creatorId: 1,
      cdate: 1494422649139,
      udate: 1494422649139,
    }))
  })
  .get('/articles', (req, res) => {
    const data = mock({
      'articles|10': [{
        id: 1,
        'category|1': [1, 2, 3],
        title: '@title',
        content: '@paragraph',
        creatorId: 1,
        cdate: 1494422649139,
        udate: 1494422649139,
      }],
    })

    data.articles.forEach((item, index) => {
      item.id = index + 1
      return item
    })

    res.json(data.articles)
  })
  .post('/articles', (req, res) => {
    res.json(Object.assign({}, req.body, {
      id: 100,
      creatorId: 1,
      cdate: 1494422649139,
      udate: 1494422649139,
    }))
  })
  .put('/articles/:id', (req, res) => {
    res.json(Object.assign({}, req.body, {
      creatorId: 1,
      cdate: 1494422649139,
      udate: 1494422649139,
    }))
  })
  .delete('/articles/:id', (req, res) => {
    res.json({ message: '删除成功' })
  })

router
  .get('/categories', (req, res) => {
    const json = mock({
      "data|3-7": [
        {
          "id|+1":1,
          "category":"@word",
          "description":"@cparagraph"
        }
      ]
    })

    return res.json(json.data)
  })

router
  .get('/applicants', (req, res) => {
    const data = mock({
      'applicants|1-4': [{
        'id|+1': 1,
        category: 'frontend',
        description: '过了 4 级',
        content: 1,
        translation: '@cparagraph',
        cdate: '@date',
      }]
    })

    return res.json(data.applicants)
  })
  .post('/applicants', (req, res) => {
    return res.json({
      email: req.body.email,
    })
  })
  .patch('/applicants/:id', (req, res) => {
    return res.json({
      id: 1,
      username: '@cname',
      opinion: '瞎胡点的',
      cdate: '@date',
      result: true,
    })
  })

router.get('/translations/pull/:status', (req, res) => {
  const data = mock({
    'articles|10': [{
      'id|+1': Math.ceil(Math.random()*100),
      title: '@ctitle',
      description: '@cparagraph',
      'category|1': ['前端', '后端', 'Android', 'iOS', '设计', '产品', '其他'],
      translator: {
        'id|+1': 1,
        name: '@cname',
        avatar: '/static/images/default-avatar.png',
      },
      'status|1': [
        0, // 等待翻译
        1, // 翻译中
        2, // 等待校对
        3, // 校对中
        4  // 已完成
      ],
      tscore: 6,
      rscore: 3,
      tduration: 3,
      rduration: 1,
      word: 1088,
      timeline:"[{\"user\":\"Zijian\",\"uid\":\"1\",\"action\":\"\\u8ba4\\u9886\\u7ffb\\u8bd1\",\"time\":\"2017-07-23 23:04:08\"},{\"user\":\"Zijian\",\"uid\":\"1\",\"action\":\"\\u8ba4\\u9886\\u6821\\u5bf9\",\"time\":\"2017-07-23 23:14:50\"},{\"user\":\"Romeo\",\"uid\":\"2\",\"action\":\"\\u8ba4\\u9886\\u6821\\u5bf9\",\"time\":\"2017-07-23 23:21:29\"}]",
      cdate: '28 分钟前',
    }],
  })

  res.json(data.articles)
})


router.get('/translations/:id', (req, res) => {
  const { id } = req.params

  const data = mock({
    id,
    title: '@ctitle',
    description: '@cparagraph',
    'category|1': ['前端', '后端', 'Android', 'iOS', '设计', '产品', '其他'],
    translator: {
      'id|+1': 1,
      name: '@cname',
      avatar: '/static/images/default-avatar.png',
    },
    'status|1': [
      0, // 等待翻译
      1, // 翻译中
      2, // 等待校对
      3, // 校对中
      4  // 已完成
    ],
    tscore: 6,
    rscore: 3,
    tduration: 3,
    rduration: 1,
    word: 1088,
    timeline:"[{\"user\":\"Zijian\",\"uid\":\"1\",\"action\":\"\\u8ba4\\u9886\\u7ffb\\u8bd1\",\"time\":\"2017-07-23 23:04:08\"},{\"user\":\"Zijian\",\"uid\":\"1\",\"action\":\"\\u8ba4\\u9886\\u6821\\u5bf9\",\"time\":\"2017-07-23 23:14:50\"},{\"user\":\"Romeo\",\"uid\":\"2\",\"action\":\"\\u8ba4\\u9886\\u6821\\u5bf9\",\"time\":\"2017-07-23 23:21:29\"}]",
    cdate: '28 分钟前',
  })

  res.json(data)
})

router.put('/translations/:id', (req, res) => {
  res.status(200).end()
})

router.post('/translations/claim/translation', (req, res) => {
  const { id } = req.body

  const data = mock({
    id,
    title: '@ctitle',
    description: '@cparagraph',
    'category|1': ['前端', '后端', 'Android', 'iOS', '设计', '产品', '其他'],
    translator: {
      'id|+1': 1,
      name: '@cname',
      avatar: '/static/images/default-avatar.png',
    },
    'status|1': [
      0, // 等待翻译
      1, // 翻译中
      2, // 等待校对
      3, // 校对中
      4  // 已完成
    ],
    tscore: 6,
    rscore: 3,
    tduration: 3,
    rduration: 1,
    word: 1088,
    timeline:"[{\"user\":\"Zijian\",\"uid\":\"1\",\"action\":\"\\u8ba4\\u9886\\u7ffb\\u8bd1\",\"time\":\"2017-07-23 23:04:08\"},{\"user\":\"Zijian\",\"uid\":\"1\",\"action\":\"\\u8ba4\\u9886\\u6821\\u5bf9\",\"time\":\"2017-07-23 23:14:50\"},{\"user\":\"Romeo\",\"uid\":\"2\",\"action\":\"\\u8ba4\\u9886\\u6821\\u5bf9\",\"time\":\"2017-07-23 23:21:29\"}]",
    cdate: '28 分钟前',
  })

  res.json(data)
})

router.post('/translations/claim/review', (req, res) => {
  const { id } = req.body

  const data = mock({
    id,
    title: '@ctitle',
    description: '@cparagraph',
    'category|1': ['前端', '后端', 'Android', 'iOS', '设计', '产品', '其他'],
    translator: {
      'id|+1': 1,
      name: '@cname',
      avatar: '/static/images/default-avatar.png',
    },
    'status|1': [
      0, // 等待翻译
      1, // 翻译中
      2, // 等待校对
      3, // 校对中
      4  // 已完成
    ],
    tscore: 6,
    rscore: 3,
    tduration: 3,
    rduration: 1,
    word: 1088,
    timeline:"[{\"user\":\"Zijian\",\"uid\":\"1\",\"action\":\"\\u8ba4\\u9886\\u7ffb\\u8bd1\",\"time\":\"2017-07-23 23:04:08\"},{\"user\":\"Zijian\",\"uid\":\"1\",\"action\":\"\\u8ba4\\u9886\\u6821\\u5bf9\",\"time\":\"2017-07-23 23:14:50\"},{\"user\":\"Romeo\",\"uid\":\"2\",\"action\":\"\\u8ba4\\u9886\\u6821\\u5bf9\",\"time\":\"2017-07-23 23:21:29\"}]",
    cdate: '28 分钟前',
  })

  res.json(data)
})

router
  .get('/recommends', (req, res) => {
    const results = mock({
      'data|1-5': [{
        "id|+1": 1,
        "category": 3,
        "title": "后端架构",
        "url": "http://www.timaticweb2.com",
        "recommender": 1,
        "recommenderName": "Romeo0906",
        "status": 0,
        "description": "描述了服务端架构的一些技巧",
        "udate": "2017-06-13 22:00:00",
        "cdate": "2017-06-13 22:00:00",
      }]
    })

    return res.json(results.data)
  })
  .post('/recommends', (req, res) => {
    return res.json({
      "id": 6,
      "category": 4,
      "title": "PHP 最佳实践",
      "url": "http://php.net",
      "recommender": 1,
      "status": 0,
      "description": "PHP 最佳实践 100 例",
      "udate": "2017-06-13 22:27:51",
      "cdate": "2017-06-13 22:27:51",
      "recommenderName": "Romeo0906"
    })
  })
  .put('/recommends/result/:id/:result', (req, res) => {
    return Math.random() > 0.5 ? res.sendStatus(200) : res.sendStatus(400)
  })

router.all('*', (req, res) => {
  res.status(404).json({ message: '404 Not found.' })
})

module.exports = router
