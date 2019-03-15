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
    stage('Set Ref to master') {
        steps {
            sh "git fetch --no-tags https://github.com/ammartins/bankcree +refs/heads/master:refs/remotes/origin/master"
        }
    }
    stage('SonarQube analysis') {
        environment {
            SONAR_TOKEN = credentials('sonar-run')
        }
        steps {
          sh "/srv/app-sonar-2.4/sonar-runner-2.4/bin/sonar-runner \
                -Dsonar.projectKey=abn-php-macOS \
                -Dsonar.organization=ammartins-github \
                -Dsonar.sources=src \
                -Dsonar.host.url=https://sonarcloud.io \
                -Dsonar.login=${SONAR_TOKEN} \
                -Dsonar.branch.name=${BRANCH_NAME} \
                -Dsonar.pullrequest.provider=github \
                -Dsonar.pullrequest.github.repository=bankcree"
        }
    }
    stage('Lint') {
      steps {
        sh '''
            phplint \'**/*.php\' \'!vendor/**\' \'!app/cache/**\'
        '''
      }
    }
    stage('PHPCS') {
      steps {
        sh '''
            # vendor/squizlabs/php_codesniffer/bin/phpcs src
        '''
      }
    }
  }
}
