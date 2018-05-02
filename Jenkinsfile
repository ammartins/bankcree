pipeline {
  agent any
  stages {
    stage('Composer') {
      steps {
        sh 'composer install --dev'
      }
    }
    stage('Install Assetics') {
      steps {
        sh '''
            php app/console assetic:dump
        '''
      }
    }
    stage('Lint') {
      steps {
        sh '''
            phplint \'**/*.php\' \'!vendor/**\' \'!app/cache/**\'
        '''
        sh '''
            composer install --dev
        '''
        sh '''
            php app/console assetic:dump web
        '''
      }
    }
  }
}
