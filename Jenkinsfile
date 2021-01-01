pipeline {
  agent any
  stages {
    stage('Composer') {
      steps {
        sh 'composer install'
      }
    }
    stage('Install Assetics') {
      steps {
        sh '''php app/console assetic:dump'''
      }
    }
    stage('Set Ref to master') {
      steps {
        sh 'git fetch --no-tags https://github.com/ammartins/bankcree +refs/heads/master:refs/remotes/origin/master'
      }
    }
    stage('Lint') {
      steps {
        sh '''phplint \'**/*.php\' \'!vendor/**\' \'!app/cache/**\''''
      }
    }
    stage('Remove Vendor Folder') {
      steps {
        sh '''rm -rf vendor'''
      }
    }
    // stage('Docker image Create') {
    //   steps {
    //     script {
    //       dockerImage = docker.build registry + ":$BUILD_NUMBER"
    //     }

    //   }
    // }
    // stage('Push Images') {
    //   parallel {
    //     stage('Deploy Image Cron') {
    //       steps {
    //         script {
    //           docker.withRegistry( '', registryCredential ) {
    //             dockerImage.push()
    //           }
    //         }
    //       }
    //     }
    //   }
    // }
  }
  environment {
    registry = 'ammartins/abnapp'
    registryCredential = 'docker-hub'
  }

}
