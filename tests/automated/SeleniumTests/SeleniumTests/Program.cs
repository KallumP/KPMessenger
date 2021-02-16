using System;
using System.Collections.Generic;
using System.Text;

namespace SeleniumTests
{
    class Program
    {

        static void Main(string[] args)
        {

            LoginTests();
            CreateAccountTests();
            Console.WriteLine("Tests Finished");
        }

        static void LoginTests()
        {
            Login.LoginCorrectCredentials();
            TestEndDelimeter();
            Login.LoginBadUsername();
            TestEndDelimeter();
            Login.LoginBadPassword();
            TestEndDelimeter();
            Login.LoginCorrectUsernameDifferentUsersPassword();
            TestEndDelimeter();
            Login.LoginNoUsername();
            TestEndDelimeter();
            Login.LoginNoPassword();
            TestEndDelimeter();
            Login.LoginNoInputs();
            TestEndDelimeter();
        }

        static void CreateAccountTests()
        {
            NewAccount.PasswordsDontMatch();
            TestEndDelimeter();
        }

        static void TestEndDelimeter()
        {
            Console.WriteLine("\n\n");
        }
    }
}
