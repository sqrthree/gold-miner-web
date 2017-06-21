import Base from './src/Base'

const Apply = (resolve) => {
  require.ensure([], (require) => {
    resolve(require('./src/Apply'))
  })
}

const Applicants = (resolve) => {
  require.ensure([], (require) => {
    resolve(require('./src/Applicants'))
  }, 'applications')
}

const Applicant = (resolve) => {
  require.ensure([], (require) => {
    resolve(require('./src/Applicant'))
  }, 'applications')
}

const Texts = (resolve) => {
  require.ensure([], (require) => {
    resolve(require('./src/Texts'))
  }, 'applications')
}

export default {
  Base,
  Apply,
  Applicants,
  Applicant,
  Texts,
}
